<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalService extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'invoice_id', 'name', 'amount', 'added_by', 'added_at',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'added_at' => 'datetime',
    ];

    // --- Relationships ---

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
