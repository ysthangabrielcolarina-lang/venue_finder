<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    // Only owners and admins may add venues.
    public function create(User $user): bool
    {
        return in_array($user->role, ['owner', 'admin'], true) && $user->is_approved;
    }

    // Admins may edit any venue. Owners only the venues linked to them in venue_user.
    public function update(User $user, Venue $venue): bool
    {
        return $user->role === 'admin'
            || $venue->owners()->whereKey($user->id)->exists();
    }
    // Who is allowed to edit a venue is also allowed to delete it
    public function delete(User $user, Venue $venue): bool
    {
        return $this->update($user, $venue);
    }
}
