<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckInOut extends Model
{
    use HasFactory;

    protected $table = 'check_in_outs';

    protected $fillable = [
        'booking_id', 'actual_check_in_at', 'actual_check_out_at',
        'checked_in_by', 'checked_out_by',
    ];

    protected $casts = [
        'actual_check_in_at'  => 'datetime',
        'actual_check_out_at' => 'datetime',
    ];

    // --- Relationships ---

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}
