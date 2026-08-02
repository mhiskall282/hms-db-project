<?php

namespace App\Actions;

use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    /**
     * Create a new booking with anti-double-booking protection.
     *
     * @param  array  $data  Validated data from CreateBookingRequest
     * @return Booking
     * @throws RoomNotAvailableException
     */
    public function execute(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            // Lock the room row to prevent concurrent overlapping bookings (FR-2.4)
            $room = Room::lockForUpdate()->findOrFail($data['room_id']);

            if ($room->status === 'maintenance') {
                throw new RoomNotAvailableException('This room is currently under maintenance.');
            }

            // Check for overlapping non-cancelled bookings
            $overlap = Booking::where('room_id', $data['room_id'])
                ->whereNotIn('status', ['cancelled'])
                ->where('check_in_date', '<', $data['check_out_date'])
                ->where('check_out_date', '>', $data['check_in_date'])
                ->exists();

            if ($overlap) {
                throw new RoomNotAvailableException(
                    'This room is already booked for the selected dates. Please choose different dates or a different room.'
                );
            }

            $booking = Booking::create([
                'guest_id'       => $data['guest_id'],
                'room_id'        => $data['room_id'],
                'check_in_date'  => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'status'         => 'confirmed',
                'created_by'     => $data['created_by'],
                'notes'          => $data['notes'] ?? null,
            ]);

            // Update room status to reserved
            $room->update(['status' => 'reserved']);

            // Send confirmation notification if guest email is present (FR-4.5)
            if ($booking->guest && $booking->guest->email) {
                try {
                    $booking->guest->notify(new \App\Notifications\BookingConfirmationNotification($booking));
                } catch (\Throwable $e) {
                    // Suppress mail transport exceptions in local/student environments
                }
            }

            return $booking;
        });
    }
}
