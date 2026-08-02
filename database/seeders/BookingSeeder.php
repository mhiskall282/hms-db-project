<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\CheckInOut;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AdditionalService;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $receptionist = User::whereHas('roles', fn($q) => $q->where('name', 'receptionist'))->first();
        $accountant   = User::whereHas('roles', fn($q) => $q->where('name', 'accountant'))->first();

        if (!$receptionist || !$accountant) {
            $this->command->error('Receptionist or accountant user not found. Run UserSeeder first.');
            return;
        }

        $guests = Guest::all()->keyBy('id_number');
        $rooms  = Room::with('roomType')->get()->keyBy('room_number');

        // =============================================
        // Booking 1: Completed (checked out) — Room 101
        // Invoice paid in full. Demonstrates full lifecycle.
        // =============================================
        $booking1 = Booking::updateOrCreate(
            ['booking_reference' => 'HMS-DEMO0001'],
            [
                'guest_id'       => $guests['GHA-0001234']->id,
                'room_id'        => $rooms['101']->id,
                'check_in_date'  => Carbon::now()->subDays(5)->toDateString(),
                'check_out_date' => Carbon::now()->subDays(2)->toDateString(),
                'status'         => 'checked_out',
                'created_by'     => $receptionist->id,
                'notes'          => 'Demo booking — completed lifecycle.',
            ]
        );

        CheckInOut::updateOrCreate(
            ['booking_id' => $booking1->id],
            [
                'actual_check_in_at'  => Carbon::now()->subDays(5)->setHour(14),
                'actual_check_out_at' => Carbon::now()->subDays(2)->setHour(11),
                'checked_in_by'       => $receptionist->id,
                'checked_out_by'      => $receptionist->id,
            ]
        );

        $nights1 = 3;
        $rate1   = $rooms['101']->roomType->base_rate;

        $invoice1 = Invoice::updateOrCreate(
            ['booking_id' => $booking1->id],
            [
                'room_charge'     => $rate1 * $nights1,
                'services_charge' => 25.00,
                'subtotal'        => ($rate1 * $nights1) + 25.00,
                'tax'             => 0.00,
                'total'           => ($rate1 * $nights1) + 25.00,
                'status'          => 'paid',
                'issued_at'       => Carbon::now()->subDays(2),
            ]
        );

        AdditionalService::updateOrCreate(
            ['booking_id' => $booking1->id, 'name' => 'Laundry Service'],
            [
                'invoice_id' => $invoice1->id,
                'amount'     => 25.00,
                'added_by'   => $receptionist->id,
                'added_at'   => Carbon::now()->subDays(3),
            ]
        );

        Payment::updateOrCreate(
            ['invoice_id' => $invoice1->id, 'method' => 'mobile_money'],
            [
                'amount'      => $invoice1->total,
                'paid_at'     => Carbon::now()->subDays(2),
                'recorded_by' => $accountant->id,
                'notes'       => 'Full payment at checkout.',
            ]
        );

        // =============================================
        // Booking 2: Currently checked in — Room 201
        // Invoice partially paid. Shows outstanding balance.
        // =============================================
        $booking2 = Booking::updateOrCreate(
            ['booking_reference' => 'HMS-DEMO0002'],
            [
                'guest_id'       => $guests['GHA-0005678']->id,
                'room_id'        => $rooms['201']->id,
                'check_in_date'  => Carbon::now()->subDays(1)->toDateString(),
                'check_out_date' => Carbon::now()->addDays(2)->toDateString(),
                'status'         => 'checked_in',
                'created_by'     => $receptionist->id,
            ]
        );
        $rooms['201']->update(['status' => 'occupied']);

        CheckInOut::updateOrCreate(
            ['booking_id' => $booking2->id],
            [
                'actual_check_in_at' => Carbon::now()->subDays(1)->setHour(15),
                'checked_in_by'      => $receptionist->id,
            ]
        );

        $nights2 = 3;
        $rate2   = $rooms['201']->roomType->base_rate;

        $invoice2 = Invoice::updateOrCreate(
            ['booking_id' => $booking2->id],
            [
                'room_charge'     => $rate2 * $nights2,
                'services_charge' => 0.00,
                'subtotal'        => $rate2 * $nights2,
                'tax'             => 0.00,
                'total'           => $rate2 * $nights2,
                'status'          => 'partial',
                'issued_at'       => Carbon::now()->subDays(1),
            ]
        );

        Payment::updateOrCreate(
            ['invoice_id' => $invoice2->id, 'method' => 'cash'],
            [
                'amount'      => 100.00, // partial payment
                'paid_at'     => Carbon::now()->subDay(),
                'recorded_by' => $accountant->id,
                'notes'       => 'Deposit on check-in.',
            ]
        );

        // =============================================
        // Booking 3: Confirmed (future) — Room 301 (Suite)
        // Today's upcoming check-in for demo.
        // =============================================
        $booking3 = Booking::updateOrCreate(
            ['booking_reference' => 'HMS-DEMO0003'],
            [
                'guest_id'       => $guests['UK-GX0001234']->id,
                'room_id'        => $rooms['301']->id,
                'check_in_date'  => Carbon::now()->toDateString(),
                'check_out_date' => Carbon::now()->addDays(3)->toDateString(),
                'status'         => 'confirmed',
                'created_by'     => $receptionist->id,
                'notes'          => 'Corporate booking — late arrival expected.',
            ]
        );
        $rooms['301']->update(['status' => 'reserved']);

        // =============================================
        // Booking 4: Future confirmed booking — Room 202
        // Demonstrates availability for tomorrow onwards.
        // =============================================
        Booking::updateOrCreate(
            ['booking_reference' => 'HMS-DEMO0004'],
            [
                'guest_id'       => $guests['AE-12345678']->id,
                'room_id'        => $rooms['202']->id,
                'check_in_date'  => Carbon::now()->addDays(3)->toDateString(),
                'check_out_date' => Carbon::now()->addDays(7)->toDateString(),
                'status'         => 'confirmed',
                'created_by'     => $receptionist->id,
            ]
        );

        $this->command->info('Demo bookings seeded with full lifecycle data.');
    }
}
