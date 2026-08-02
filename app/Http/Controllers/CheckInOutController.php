<?php

namespace App\Http\Controllers;

use App\Actions\CheckInAction;
use App\Actions\CheckOutAction;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInOutController extends Controller
{
    public function __construct(
        private CheckInAction  $checkIn,
        private CheckOutAction $checkOut
    ) {}

    /**
     * Today's arrivals and departures list.
     */
    public function index(): View
    {
        $arrivals   = Booking::with(['guest', 'room.roomType'])
            ->whereIn('status', ['confirmed'])
            ->where('check_in_date', '<=', today())
            ->orderBy('check_in_date')
            ->get();

        $departures = Booking::with(['guest', 'room.roomType', 'invoice'])
            ->where('status', 'checked_in')
            ->where('check_out_date', '<=', today())
            ->orderBy('check_out_date')
            ->get();

        return view('check-in-out.index', compact('arrivals', 'departures'));
    }

    /**
     * Process check-in (FR-5.1, FR-5.3).
     */
    public function checkIn(Booking $booking): RedirectResponse
    {
        try {
            $this->checkIn->execute($booking, auth()->id());
            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', "Guest checked in successfully. Room {$booking->room->room_number} is now Occupied.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process check-out (FR-5.2, FR-5.3).
     */
    public function checkOut(Booking $booking): RedirectResponse
    {
        try {
            $result = $this->checkOut->execute($booking, auth()->id());
            return redirect()
                ->route('invoices.show', $result['invoice'])
                ->with('success', "Guest checked out. Room {$booking->room->room_number} is now Dirty. Invoice generated.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
