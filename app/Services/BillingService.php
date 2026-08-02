<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Support\Carbon;

class BillingService
{
    /**
     * Calculate the full billing breakdown for a booking.
     *
     * @return array{nights: int, rate: float, roomCharge: float, servicesTotal: float, subtotal: float, tax: float, total: float}
     */
    public function calculateTotal(Booking $booking): array
    {
        $booking->loadMissing(['room.roomType', 'additionalServices']);

        $rate           = (float) $booking->room->roomType->base_rate;
        $nights         = $booking->nights;
        $roomCharge     = $rate * $nights;
        $servicesTotal  = $booking->additionalServices->sum(fn($s) => (float) $s->amount);
        $subtotal       = $roomCharge + $servicesTotal;
        $taxRate        = (float) config('hms.tax_rate', 0.00);
        $tax            = round($subtotal * $taxRate, 2);
        $total          = round($subtotal + $tax, 2);

        return compact('nights', 'rate', 'roomCharge', 'servicesTotal', 'subtotal', 'tax', 'total');
    }

    /**
     * Create or update an invoice for a booking based on current charges.
     */
    public function generateInvoice(Booking $booking): Invoice
    {
        $breakdown = $this->calculateTotal($booking);

        $invoice = Invoice::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'room_charge'     => $breakdown['roomCharge'],
                'services_charge' => $breakdown['servicesTotal'],
                'subtotal'        => $breakdown['subtotal'],
                'tax'             => $breakdown['tax'],
                'total'           => $breakdown['total'],
                'issued_at'       => now(),
            ]
        );

        // Don't change status if already partial/paid
        if ($invoice->status === 'unpaid' || $invoice->wasRecentlyCreated) {
            $invoice->status = 'unpaid';
            $invoice->save();
        }

        return $invoice;
    }

    /**
     * Recalculate invoice totals after adding an additional service.
     * Updates room_charge, services_charge, subtotal, tax, and total.
     * Does NOT change the payment status.
     */
    public function recalculateInvoice(Invoice $invoice): Invoice
    {
        $booking    = $invoice->booking()->with(['room.roomType', 'additionalServices'])->first();
        $breakdown  = $this->calculateTotal($booking);

        $invoice->update([
            'room_charge'     => $breakdown['roomCharge'],
            'services_charge' => $breakdown['servicesTotal'],
            'subtotal'        => $breakdown['subtotal'],
            'tax'             => $breakdown['tax'],
            'total'           => $breakdown['total'],
        ]);

        // Re-evaluate status after amount change
        $invoice->recalculateStatus();

        return $invoice->fresh();
    }
}
