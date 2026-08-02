<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;

class Guest extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = ['name', 'phone', 'email', 'id_number', 'nationality', 'notes'];

    // --- Relationships ---

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(GuestReview::class);
    }

    // --- VIP Loyalty Tier Calculation ---

    public function getCompletedStaysCountAttribute(): int
    {
        return $this->bookings()->where('status', 'checked_out')->count();
    }

    public function getTotalSpendAttribute(): float
    {
        return (float) $this->bookings()
            ->whereHas('invoice', fn($q) => $q->where('status', 'paid'))
            ->get()
            ->sum(fn($b) => $b->invoice->total ?? 0);
    }

    public function getLoyaltyTierAttribute(): string
    {
        $stays = $this->completed_stays_count;
        $spend = $this->total_spend;

        if ($stays >= 10 || $spend >= 5000) {
            return 'Platinum VIP';
        }
        if ($stays >= 5 || $spend >= 2500) {
            return 'Gold VIP';
        }
        if ($stays >= 2 || $spend >= 1000) {
            return 'Silver Member';
        }

        return 'Standard Guest';
    }

    public function getDiscountPercentageAttribute(): int
    {
        return match($this->loyalty_tier) {
            'Platinum VIP'  => 15,
            'Gold VIP'      => 10,
            'Silver Member' => 5,
            default         => 0,
        };
    }

    // --- Scopes ---

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('id_number', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
