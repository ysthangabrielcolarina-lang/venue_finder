<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    // status and expires_at are NOT here on purpose: the system sets them,
    // so a customer can't submit status=confirmed.
    protected $fillable = [
        'venue_id', 'user_id', 'start_date', 'end_date',
        'guest_count', 'receipt_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'expires_at' => 'datetime',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Pending requests whose 24-hour hold ran out become 'expired'.
    public static function expireStale(): void
    {
        static::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    // Reservations that currently block a date: confirmed, or pending and still on hold.
    public function scopeBlocking(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'confirmed')
              ->orWhere(function ($q) {
                  $q->where('status', 'pending')->where('expires_at', '>', now());
              });
        });
    }

    // Two date ranges overlap when each one starts before the other ends.
    public function scopeOverlapping(Builder $query, $start, $end): Builder
    {
        return $query->where('start_date', '<=', $end)
                     ->where('end_date', '>=', $start);
    }
}