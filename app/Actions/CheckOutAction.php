<?php

namespace App\Actions;

use App\Models\Booking;
use App\Models\CheckInOut;
use App\Models\Room;
use App\Services\BillingService;
use Illuminate\Support\Facades\DB;

class CheckOutAction
{
    public function __construct(private BillingService $billingService) {}

    /**
     * Perform check-out for a checked-in booking.
     * Atomically updates booking status, room status, records check-out timestamp,
     * and generates/updates the invoice (FR-5.2, FR-5.3, FR-6.1).
     *
     * @param  Booking  $booking    Must be in 'checked_in' status
     * @param  int      $staffId    User ID of the staff performing check-out
     * @return array{checkInOut: CheckInOut, invoice: \App\Models\Invoice}
     */
    public function execute(Booking $booking, int $staffId): array
    {
        return DB::transaction(function () use ($booking, $staffId) {
            $booking = Booking::lockForUpdate()->find($booking->id);

            if ($booking->status !== 'checked_in') {
                throw new \RuntimeException("Cannot check out: booking status is '{$booking->status}'.");
            }

            // Update booking status
            $booking->update(['status' => 'checked_out']);

            // Update room status to dirty (needs cleaning — FR-5.2)
            Room::lockForUpdate()->find($booking->room_id)->update(['status' => 'dirty']);

            // Update check-out timestamp
            $checkInOut = CheckInOut::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'actual_check_out_at' => now(),
                    'checked_out_by'      => $staffId,
                ]
            );

            // Generate or update invoice (FR-6.1)
            $booking->load(['room.roomType', 'additionalServices']);
            $invoice = $this->billingService->generateInvoice($booking);

            return compact('checkInOut', 'invoice');
        });
    }
}
