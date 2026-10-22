<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        Reservation::expireStale();

        $user = $request->user();
        $data = ['user' => $user, 'stats' => [], 'myVenues' => collect()];

        if ($user->role === 'customer') {
            $data['stats'] = [
                'pending' => Reservation::where('user_id', $user->id)->where('status', 'pending')->count(),
                'confirmed' => Reservation::where('user_id', $user->id)->where('status', 'confirmed')->count(),
                'total' => Reservation::where('user_id', $user->id)->count(),
            ];
        } elseif ($user->role === 'owner') {
            $venueIds = $user->venues()->pluck('venues.id');

            $data['stats'] = [
                'venues' => $venueIds->count(),
                'pending' => Reservation::whereIn('venue_id', $venueIds)->where('status', 'pending')->count(),
                'confirmed' => Reservation::whereIn('venue_id', $venueIds)->where('status', 'confirmed')->count(),
            ];
            $data['myVenues'] = $user->venues()->with('barangay')->orderBy('name')->get();
        } elseif ($user->role === 'admin') {
            $data['stats'] = [
                'venues' => Venue::count(),
                'pending' => Reservation::where('status', 'pending')->count(),
                'users' => User::count(),
            ];
        }

        return view('dashboard', $data);
    }
}
