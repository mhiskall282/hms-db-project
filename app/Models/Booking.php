<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference', 'guest_id', 'room_id', 'check_in_date',
        'check_out_date', 'status', 'created_by', 'notes',
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
    ];

    // --- Boot ---

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = static::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'HMS-' . strtoupper(Str::random(8));
        } while (static::withTrashed()->where('booking_reference', $ref)->exists());

        return $ref;
    }

    // --- Relationships ---

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkInOut(): HasOne
    {
        return $this->hasOne(CheckInOut::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function additionalServices(): HasMany
    {
        return $this->hasMany(AdditionalService::class);
    }

    // --- Computed Attributes ---

    /**
     * Number of nights for this booking.
     */
    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    // --- Scopes ---

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['cancelled', 'checked_out']);
    }

    public function scopeOverlapping(Builder $query, string $from, string $to, int $roomId): Builder
    {
        return $query->where('room_id', $roomId)
            ->whereNotIn('status', ['cancelled'])
            ->where('check_in_date', '<', $to)
            ->where('check_out_date', '>', $from);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
