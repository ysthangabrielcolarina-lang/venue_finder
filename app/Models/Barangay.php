<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude'];

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function travelTimes(): HasMany
    {
        return $this->hasMany(TravelTime::class);
    }
}
