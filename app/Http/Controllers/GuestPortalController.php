<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestPortalController extends Controller
{
    /**
     * Display public booking lookup form.
     */
    public function lookupForm(): View
    {
        return view('guest-portal.lookup');
    }

    /**
     * Search booking by reference & phone/email.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'booking_reference' => ['required', 'string'],
            'contact'           => ['required', 'string'],
        ]);

        $booking = Booking::with(['guest', 'room.roomType', 'invoice.payments', 'additionalServices'])
            ->where('booking_reference', trim($validated['booking_reference']))
            ->whereHas('guest', function ($q) use ($validated) {
                $contact = trim($validated['contact']);
                $q->where('phone', 'like', "%{$contact}%")
                  ->orWhere('email', 'like', "%{$contact}%")
                  ->orWhere('id_number', 'like', "%{$contact}%");
            })
            ->first();

        if (!$booking) {
            return back()->with('error', 'No reservation found matching the reference number and contact info provided.');
        }

        return view('guest-portal.show', compact('booking'));
    }

    /**
     * Public PDF download for guest invoice.
     */
    public function downloadPdf(Booking $booking)
    {
        if (!$booking->invoice) {
            return back()->with('error', 'Invoice not yet generated for this booking.');
        }

        $invoice = $booking->invoice;
        $hotel = config('hms.hotel_name', 'Grand Hotel HMS');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'hotel'));

        return $pdf->download("Invoice-{$booking->booking_reference}.pdf");
    }
}
