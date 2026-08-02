<?php

namespace App\Actions;

use App\Models\Booking;
use App\Models\CheckInOut;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class CheckInAction
{
    /**
     * Perform check-in for a confirmed booking.
     * Atomically updates booking status, room status, and records the check-in timestamp.
     *
     * @param  Booking  $booking    Must be in 'confirmed' status
     * @param  int      $staffId    User ID of the staff performing check-in
     * @return CheckInOut
     */
    public function execute(Booking $booking, int $staffId): CheckInOut
    {
        return DB::transaction(function () use ($booking, $staffId) {
            // Reload with lock to prevent concurrent check-in
            $booking = Booking::lockForUpdate()->find($booking->id);

            if ($booking->status !== 'confirmed') {
                throw new \RuntimeException("Cannot check in: booking status is '{$booking->status}'.");
            }

            // Update booking status to checked_in
            $booking->update(['status' => 'checked_in']);

            // Update room status to occupied
            Room::lockForUpdate()->find($booking->room_id)->update(['status' => 'occupied']);

            // Create or update check-in/out record
            $record = CheckInOut::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'actual_check_in_at' => now(),
                    'checked_in_by'      => $staffId,
                ]
            );

            return $record;
        });
    }
}
