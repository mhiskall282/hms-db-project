<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'room_type_id', 'status', 'floor'];

    protected $casts = [
        'floor' => 'integer',
    ];

    // --- Relationships ---

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->latest();
    }

    // --- Scopes ---

    /**
     * Rooms available for the given date range.
     * Excludes rooms under maintenance and rooms with overlapping non-cancelled bookings.
     */
    public function scopeAvailable(Builder $query, Carbon|string $from, Carbon|string $to, ?int $roomTypeId = null): Builder
    {
        return $query
            ->where('status', '!=', 'maintenance')
            ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
            ->whereDoesntHave('bookings', function (Builder $q) use ($from, $to) {
                $q->whereNotIn('status', ['cancelled'])
                  ->where('check_in_date', '<', $to)
                  ->where('check_out_date', '>', $from);
            });
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
