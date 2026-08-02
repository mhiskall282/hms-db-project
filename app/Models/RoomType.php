<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'base_rate', 'capacity', 'description'];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'capacity'  => 'integer',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
