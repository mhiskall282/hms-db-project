<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'room_charge', 'services_charge', 'subtotal',
        'tax', 'total', 'status', 'issued_at',
    ];

    protected $casts = [
        'room_charge'     => 'decimal:2',
        'services_charge' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'tax'             => 'decimal:2',
        'total'           => 'decimal:2',
        'issued_at'       => 'datetime',
    ];

    // --- Relationships ---

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // --- Computed Attributes ---

    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getOutstandingAttribute(): float
    {
        return max(0, (float) $this->total - $this->amount_paid);
    }

    // --- Scopes ---

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['unpaid', 'partial']);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // --- Methods ---

    /**
     * Recalculate and update the invoice status based on payments received.
     */
    public function recalculateStatus(): void
    {
        $paid = $this->payments()->sum('amount');

        $this->status = match (true) {
            $paid <= 0          => 'unpaid',
            $paid < $this->total => 'partial',
            default             => 'paid',
        };

        $this->save();
    }
}
