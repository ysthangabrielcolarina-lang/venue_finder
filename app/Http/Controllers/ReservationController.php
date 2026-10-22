<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Venue;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    // Customers see their own, owners see their venues', admins see all.
    public function index(Request $request)
    {
        Reservation::expireStale();

        $user = $request->user();
        $query = Reservation::with(['venue', 'user'])->latest();

        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'owner') {
            $query->whereIn('venue_id', $user->venues()->pluck('venues.id'));
        }

        return view('reservations.index', [
            'reservations' => $query->paginate(10),
            'title' => match ($user->role) {
                'owner' => 'Reservation requests',
                'admin' => 'All reservations',
                default => 'My reservations',
            },
            'showCustomer' => ! $user->isCustomer(),
        ]);
    }

    public function create(Venue $venue)
    {
        $this->ensureReservable($venue);

        return view('reservations.create', compact('venue'));
    }

    public function store(StoreReservationRequest $request, Venue $venue)
    {
        $this->ensureReservable($venue);
        Reservation::expireStale();

        $data = $request->validated();
        $reservation = null;

        try {
            DB::transaction(function () use ($request, $venue, $data, &$reservation) {
                // Lock this venue's row so two customers can't book the same dates at once.
                Venue::whereKey($venue->id)->lockForUpdate()->first();

                $taken = Reservation::datesTaken($venue->id, $data['start_date'], $data['end_date']);

                if ($taken) {
                    throw ValidationException::withMessages([
                        'start_date' => 'Those dates are already booked or on hold. Please pick other dates.',
                    ]);
                }

                // Only safe fields are mass assigned. The system sets everything else.
                $reservation = new Reservation([
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'guest_count' => $data['guest_count'],
                ]);
                $reservation->venue_id = $venue->id;
                $reservation->user_id = $request->user()->id;
                $reservation->status = 'pending';
                $reservation->expires_at = now()->addDay();

                if ($request->hasFile('receipt')) {
                    // Stored on the private disk: not reachable by a public URL.
                    $reservation->receipt_path = $request->file('receipt')->store('receipts');
                }

                $reservation->save();
            });
        } catch (QueryException $e) {
            // The database trigger is the last line of defense.
            if (str_contains($e->getMessage(), 'Venue already booked')) {
                throw ValidationException::withMessages([
                    'start_date' => 'Those dates are already booked or on hold. Please pick other dates.',
                ]);
            }

            throw $e;
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('status', 'Request sent. The dates are held for 24 hours while the owner reviews it.');
    }

    public function show(Reservation $reservation)
    {
        Gate::authorize('view', $reservation);
        Reservation::expireStale();

        $reservation->refresh()->load(['venue.barangay', 'user']);

        return view('reservations.show', compact('reservation'));
    }

    // The edit form: only for the customer's own pending request.
    public function edit(Reservation $reservation)
    {
        Reservation::expireStale();
        $reservation->refresh(); // pick up an expiry that just happened

        Gate::authorize('update', $reservation);

        $reservation->load('venue');

        return view('reservations.edit', compact('reservation'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $data = $request->validated();
        $venue = $reservation->venue;

        $oldReceipt = $reservation->receipt_path;
        $newReceipt = null;

        try {
            DB::transaction(function () use ($request, $reservation, $venue, $data, &$newReceipt) {
                // Same lock as a new request, so two people can't take the same dates at once.
                Venue::whereKey($venue->id)->lockForUpdate()->first();

                // The owner may have confirmed or rejected it while the form was open.
                $reservation->refresh();
                if ($reservation->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'start_date' => 'This request can no longer be edited.',
                    ]);
                }

                // Same overlap check as a new request, ignoring this reservation itself.
                $taken = Reservation::datesTaken($venue->id, $data['start_date'], $data['end_date'], $reservation->id);

                if ($taken) {
                    throw ValidationException::withMessages([
                        'start_date' => 'Those dates are already booked or on hold. Please pick other dates.',
                    ]);
                }

                // Only these three fields are filled. status and expires_at are never touched,
                // so the 24-hour hold does not restart when someone edits.
                $reservation->fill([
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'guest_count' => $data['guest_count'],
                ]);

                if ($request->hasFile('receipt')) {
                    $newReceipt = $request->file('receipt')->store('receipts');
                    $reservation->receipt_path = $newReceipt;
                }

                $reservation->save();
            });
        } catch (QueryException $e) {
            // The database trigger is the last line of defense.
            if ($newReceipt) {
                Storage::delete($newReceipt);
            }

            if (str_contains($e->getMessage(), 'Venue already booked')) {
                throw ValidationException::withMessages([
                    'start_date' => 'Those dates are already booked or on hold. Please pick other dates.',
                ]);
            }

            throw $e;
        }

        // The old receipt is removed only after the new one is saved.
        if ($newReceipt && $oldReceipt) {
            Storage::delete($oldReceipt);
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('status', 'Reservation updated.');
    }

    public function confirm(Reservation $reservation)
    {
        return $this->decide($reservation, 'confirmed', 'Reservation confirmed.');
    }

    public function reject(Reservation $reservation)
    {
        return $this->decide($reservation, 'rejected', 'Reservation rejected.');
    }

    public function cancel(Reservation $reservation)
    {
        Gate::authorize('cancel', $reservation);

        $reservation->status = 'cancelled';
        $reservation->expires_at = null;
        $reservation->save();

        return back()->with('status', 'Reservation cancelled.');
    }

    public function destroy(Reservation $reservation)
    {
        Gate::authorize('delete', $reservation);

        if ($reservation->receipt_path) {
            Storage::delete($reservation->receipt_path);
        }
        $reservation->delete();

        return redirect()->route('reservations.index')->with('status', 'Reservation deleted.');
    }

    // Receipts are private, so they are streamed through here after a permission check.
    public function receipt(Reservation $reservation)
    {
        Gate::authorize('view', $reservation);

        abort_unless($reservation->receipt_path && Storage::exists($reservation->receipt_path), 404);

        return Storage::response($reservation->receipt_path);
    }

    private function decide(Reservation $reservation, string $newStatus, string $message)
    {
        Gate::authorize('manage', $reservation);
        Reservation::expireStale();
        $reservation->refresh();

        if ($reservation->status !== 'pending') {
            return back()->withErrors(['status' => 'Only a pending request can be '.$newStatus.'.']);
        }

        $reservation->status = $newStatus;
        $reservation->expires_at = null;

        try {
            $reservation->save();
        } catch (QueryException $e) {
            // Confirming can hit the trigger if another booking took the dates first.
            if (str_contains($e->getMessage(), 'Venue already booked')) {
                return back()->withErrors(['status' => 'Another booking already holds these dates, so this request cannot be '.$newStatus.'.']);
            }

            throw $e;
        }

        return back()->with('status', $message);
    }

    // A venue can be reserved online only when it has a participating owner.
    private function ensureReservable(Venue $venue): void
    {
        abort_unless(
            $venue->isReservable(),
            403,
            'This venue is information-only and cannot be reserved online.'
        );
    }
}
