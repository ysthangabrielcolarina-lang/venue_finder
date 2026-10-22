<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class OwnerAreaController extends Controller
{
    public function __invoke(Request $request)
    {
        // Requests whose 24-hour hold ran out become 'expired' before we count them.
        Reservation::expireStale();

        // The owner's own venues, with the number of pending and confirmed bookings each.
        $venues = $request->user()->venues()
            ->with('barangay')
            ->withCount([
                'reservations as pending_count' => fn ($query) => $query->where('status', 'pending'),
                'reservations as confirmed_count' => fn ($query) => $query->where('status', 'confirmed'),
            ])
            ->orderBy('name')
            ->get();

        // Pending requests for those venues, the ones expiring soonest first.
        $pending = Reservation::with(['venue', 'user'])
            ->whereIn('venue_id', $venues->pluck('id'))
            ->where('status', 'pending')
            ->oldest('expires_at')
            ->limit(10)
            ->get();

        return view('owner.index', compact('venues', 'pending'));
    }
}
