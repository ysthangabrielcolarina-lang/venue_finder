<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
        'end_date' => 'date',
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

    // Asks the database whether the dates clash with a confirmed booking or a pending hold.
    // It uses a database function, because row-level security hides other people's reservations
    // from a normal query, and the function returns only yes or no.
    public static function datesTaken(int $venueId, string $start, string $end, ?int $exceptId = null): bool
    {
        $row = DB::selectOne(
            'select dates_are_taken(?::bigint, ?::date, ?::date, ?::bigint) as taken',
            [$venueId, $start, $end, $exceptId]
        );

        return (bool) $row->taken;
    }
}
