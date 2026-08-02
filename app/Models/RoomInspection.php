<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'inspector_id',
        'linen_changed',
        'bathroom_sanitized',
        'amenities_restocked',
        'appliances_checked',
        'minibar_checked',
        'notes',
        'inspected_at',
    ];

    protected $casts = [
        'linen_changed'       => 'boolean',
        'bathroom_sanitized'  => 'boolean',
        'amenities_restocked' => 'boolean',
        'appliances_checked'  => 'boolean',
        'minibar_checked'     => 'boolean',
        'inspected_at'        => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
