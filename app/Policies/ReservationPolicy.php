<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    // The customer who made it, the venue's owners, and admins.
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->role === 'admin'
            || $reservation->user_id === $user->id
            || $this->ownsVenue($user, $reservation);
    }

    // Confirm or reject: the venue's owners and admins.
    public function manage(User $user, Reservation $reservation): bool
    {
        return $user->role === 'admin' || $this->ownsVenue($user, $reservation);
    }

    // Edit: only the customer who made it, while it is pending and the 24-hour hold is still running.
    public function update(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id
            && $reservation->status === 'pending'
            && $reservation->expires_at !== null
            && $reservation->expires_at->isFuture();
    }

    // Cancel: only the customer who made it, while it is pending or confirmed.
    public function cancel(User $user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id
            && in_array($reservation->status, ['pending', 'confirmed'], true);
    }

    // Delete: admins any time; customers only requests that are not confirmed.
    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->role === 'admin'
            || ($reservation->user_id === $user->id
                && in_array($reservation->status, ['pending', 'rejected', 'cancelled', 'expired'], true));
    }

    private function ownsVenue(User $user, Reservation $reservation): bool
    {
        return $reservation->venue->owners()->whereKey($user->id)->exists();
    }
}
