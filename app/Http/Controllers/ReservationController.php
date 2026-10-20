<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Venue;
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

        DB::transaction(function () use ($request, $venue, $data, &$reservation) {
            // Lock this venue's row so two customers can't book the same dates at once.
            Venue::whereKey($venue->id)->lockForUpdate()->first();

            $taken = Reservation::where('venue_id', $venue->id)
                ->blocking()
                ->overlapping($data['start_date'], $data['end_date'])
                ->exists();

            if ($taken) {
                throw ValidationException::withMessages([
                    'start_date' => 'Those dates are already booked or on hold. Please pick other dates.',
                ]);
            }

            // Only safe fields are mass assigned. The system sets everything else.
            $reservation = new Reservation([
                'start_date'  => $data['start_date'],
                'end_date'    => $data['end_date'],
                'guest_count' => $data['guest_count'],
            ]);
            $reservation->venue_id   = $venue->id;
            $reservation->user_id    = $request->user()->id;
            $reservation->status     = 'pending';
            $reservation->expires_at = now()->addDay();

            if ($request->hasFile('receipt')) {
                // Stored on the private disk: not reachable by a public URL.
                $reservation->receipt_path = $request->file('receipt')->store('receipts');
            }

            $reservation->save();
        });

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
        $reservation->save();

        return back()->with('status', $message);
    }

    // Venues without a participating owner stay information-only.
    private function ensureReservable(Venue $venue): void
    {
        abort_unless(
            $venue->owners()->exists(),
            403,
            'This venue is information-only and cannot be reserved online.'
        );
    }
}