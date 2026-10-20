<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    // is_verified is NOT here on purpose: only an admin may change it.
    protected $fillable = [
        'barangay_id', 'name', 'address', 'capacity',
        'base_price', 'down_payment', 'gcash_number',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'venue_user');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function travelTimes(): HasMany
    {
        return $this->hasMany(TravelTime::class);
    }
}