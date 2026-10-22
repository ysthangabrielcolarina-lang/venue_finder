<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    // A venue can be reserved online only when it has a participating owner.
    // It uses owners_count when withCount()/loadCount() already loaded it,
    // so the list and detail pages run no extra query.
    public function isReservable(): bool
    {
        $count = $this->owners_count ?? $this->owners()->count();

        return $count > 0;
    }

    // Filters for the venue list. Each filter applies only when it has a usable value.
    public function scopeSearch(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['q'] ?? null), function (Builder $query) use ($filters) {
                $term = '%'.$filters['q'].'%';

                $query->where(fn (Builder $inner) => $inner
                    ->where('name', 'ilike', $term)
                    ->orWhere('address', 'ilike', $term));
            })
            ->when(is_numeric($filters['barangay'] ?? null), fn (Builder $query) => $query->where('barangay_id', (int) $filters['barangay']))
            ->when(is_numeric($filters['guests'] ?? null), fn (Builder $query) => $query->where('capacity', '>=', (int) $filters['guests']))
            ->when(is_numeric($filters['max_price'] ?? null), fn (Builder $query) => $query->where('base_price', '<=', (float) $filters['max_price']));
    }
}
