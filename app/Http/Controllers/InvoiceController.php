<?php

namespace App\Http\Controllers;

use App\Actions\RecordPaymentAction;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\AdditionalService;
use App\Services\BillingService;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\StoreAdditionalServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private BillingService      $billing,
        private RecordPaymentAction $recordPayment
    ) {}

    public function index(Request $request): View
    {
        $invoices = Invoice::with(['booking.guest', 'payments'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('issued_at')
            ->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['booking.guest', 'booking.room.roomType', 'booking.additionalServices.addedBy', 'payments.recordedBy']);
        $breakdown = $this->billing->calculateTotal($invoice->booking);
        return view('invoices.show', compact('invoice', 'breakdown'));
    }

    /**
     * Generate invoice for a booking (FR-6.1).
     */
    public function generate(Booking $booking): RedirectResponse
    {
        $invoice = $this->billing->generateInvoice($booking);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated.');
    }

    /**
     * Record a payment against an invoice (FR-6.3).
     */
    public function recordPayment(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $this->recordPayment->execute($invoice, array_merge(
                $request->validated(),
                ['recorded_by' => auth()->id()]
            ));

            $invoice->refresh();
            $status = $invoice->status;

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', "Payment recorded. Invoice status: " . ucfirst($status) . ".");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Add an additional service charge to a booking (FR-6.2).
     */
    public function addService(StoreAdditionalServiceRequest $request, Booking $booking): RedirectResponse
    {
        $service = AdditionalService::create([
            'booking_id' => $booking->id,
            'invoice_id' => $booking->invoice?->id,
            'name'       => $request->name,
            'amount'     => $request->amount,
            'added_by'   => auth()->id(),
            'added_at'   => now(),
        ]);

        // Recalculate invoice if one exists
        if ($booking->invoice) {
            $this->billing->recalculateInvoice($booking->invoice);
        }

        return back()->with('success', "Service '{$request->name}' added to booking.");
    }

    /**
     * Download invoice as PDF (FR-6.4).
     */
    public function download(Invoice $invoice)
    {
        $invoice->load(['booking.guest', 'booking.room.roomType', 'booking.additionalServices', 'payments']);
        $hotel = config('hms.hotel_name', 'Grand Hotel HMS');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'hotel'))
            ->setPaper('a4');

        return $pdf->download("invoice-{$invoice->id}-{$invoice->booking->booking_reference}.pdf");
    }
}
