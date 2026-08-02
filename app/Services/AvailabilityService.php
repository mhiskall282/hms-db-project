<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    /**
     * Get rooms available for the given date range.
     * Excludes rooms under maintenance and rooms with overlapping non-cancelled bookings.
     *
     * @param  Carbon|string  $from    Check-in date (inclusive)
     * @param  Carbon|string  $to      Check-out date (exclusive)
     * @param  int|null       $roomTypeId  Filter by room type (optional)
     * @return Collection<int, Room>
     */
    public function getAvailableRooms(
        Carbon|string $from,
        Carbon|string $to,
        ?int $roomTypeId = null
    ): Collection {
        return Room::query()
            ->with('roomType')
            ->available($from, $to, $roomTypeId)
            ->orderBy('room_number')
            ->get();
    }

    /**
     * Check whether a specific room is available for the given date range.
     * Excludes the given bookingId (used when modifying an existing booking).
     */
    public function isRoomAvailable(
        int $roomId,
        string $from,
        string $to,
        ?int $excludeBookingId = null
    ): bool {
        $room = Room::find($roomId);

        if (!$room || $room->status === 'maintenance') {
            return false;
        }

        $query = $room->bookings()
            ->whereNotIn('status', ['cancelled'])
            ->where('check_in_date', '<', $to)
            ->where('check_out_date', '>', $from);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }
}
