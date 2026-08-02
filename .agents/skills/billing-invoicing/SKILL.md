---
name: billing-invoicing
description: "Use this skill when working on charges, invoices, payments, or receipts."
---

# Billing & Invoicing Skill

## Purpose

This skill captures all billing domain rules: how charges are calculated, how invoices progress through states, how payments are recorded, and what constraints govern over-payment prevention.

---

## Charge Calculation Formula (FR-6.1)

```
total_room_charge = room_type.base_rate × number_of_nights
additional_services_total = SUM(additional_service.amount)
subtotal = total_room_charge + additional_services_total
tax = subtotal × TAX_RATE   (default: 0% — placeholder, flagged in PROGRESS.md)
total = subtotal + tax
```

### Number of Nights

```php
$nights = Carbon::parse($booking->check_in_date)
    ->diffInDays(Carbon::parse($booking->check_out_date));
// check_out_date is exclusive; a 1-night stay has check_in=May 1, check_out=May 2 → 1 night
```

### BillingService::calculateTotal()

```php
public function calculateTotal(Booking $booking): array
{
    $roomType = $booking->room->roomType;
    $nights   = Carbon::parse($booking->check_in_date)
                    ->diffInDays(Carbon::parse($booking->check_out_date));

    $roomCharge     = $roomType->base_rate * $nights;
    $servicesTotal  = $booking->additionalServices->sum('amount');
    $subtotal       = $roomCharge + $servicesTotal;
    $taxRate        = config('hms.tax_rate', 0.00); // 0% default
    $tax            = round($subtotal * $taxRate, 2);
    $total          = $subtotal + $tax;

    return compact('roomCharge', 'servicesTotal', 'subtotal', 'tax', 'total', 'nights');
}
```

---

## Invoice Status Transitions

```
┌────────┐    (first partial payment)    ┌─────────┐    (balance cleared)    ┌──────┐
│ unpaid │ ─────────────────────────────► │ partial │ ─────────────────────► │ paid │
└────────┘                               └─────────┘                         └──────┘
```

### Transition Rules

1. `unpaid → partial`: A payment is recorded that is less than `invoice.total`.
2. `partial → paid`: A payment is recorded that brings `amount_paid = invoice.total`.
3. `unpaid → paid`: A single full payment.
4. **No refunds in this version.** Invoice status never moves backwards.
5. Invoice is created automatically when check-out occurs (or can be pre-generated for early payment).

### Invoice Status Update Logic

After recording a payment, recalculate the invoice status:

```php
$amountPaid = $invoice->payments->sum('amount');

$invoice->status = match(true) {
    $amountPaid <= 0                    => 'unpaid',
    $amountPaid < $invoice->total       => 'partial',
    $amountPaid >= $invoice->total      => 'paid',
};
$invoice->save();
```

---

## Payment Validation Rules (FR-6.5)

### Rule: Payment Must Not Exceed Outstanding Balance

```php
// In RecordPaymentAction::execute()
$amountPaid   = $invoice->payments->sum('amount');
$outstanding  = $invoice->total - $amountPaid;

if ($data['amount'] > $outstanding) {
    throw new ValidationException::withMessages([
        'amount' => "Payment of {$data['amount']} exceeds outstanding balance of {$outstanding}."
    ]);
}
```

This ensures:
- A guest's balance cannot go negative.
- Overpayment is prevented at the Action level (before DB write).

### Payment Methods (FR-6.3)

| Method | Enum Value | Notes |
|--------|-----------|-------|
| Cash | `cash` | Default; no external processing |
| Card | `card` | Simulated; no real gateway |
| Mobile Money | `mobile_money` | Simulated; common in Ghana |

---

## Additional Services (FR-6.2)

Additional services are line items added to a booking that become part of the invoice subtotal.

Rules:
1. Services can be added at any point from booking creation through check-out.
2. Adding a service to an already-issued invoice triggers invoice recalculation.
3. Invoice `subtotal`, `tax`, and `total` are recalculated when a service is added.
4. Services may be added by Receptionist or Accountant (check Policy).

```php
// AdditionalService model
protected $fillable = ['booking_id', 'invoice_id', 'name', 'amount', 'added_by', 'added_at'];

// After adding a service, recalculate invoice
public function recalculate(): void
{
    $this->subtotal = $this->booking->additionalServices->sum('amount')
                    + ($this->booking->room->roomType->base_rate
                       * $this->booking->nights());
    $this->tax      = round($this->subtotal * config('hms.tax_rate', 0), 2);
    $this->total    = $this->subtotal + $this->tax;
    $this->save();
}
```

---

## Invoice PDF Generation (FR-6.4)

Use `barryvdh/laravel-dompdf`.

```php
// InvoiceController::download()
public function download(Invoice $invoice)
{
    $this->authorize('export', $invoice);

    $data = [
        'invoice'  => $invoice->load('booking.guest', 'booking.room.roomType', 'payments', 'booking.additionalServices'),
        'hotel'    => config('hms.hotel_name', 'Grand Hotel'),
    ];

    $pdf = Pdf::loadView('invoices.pdf', $data);
    return $pdf->download("invoice-{$invoice->id}.pdf");
}
```

The PDF view (`resources/views/invoices/pdf.blade.php`) must include:
- Hotel name and address header
- Guest name and booking reference
- Itemized charges (room charge, additional services, tax)
- Total due and amount paid
- Outstanding balance
- Payment method and date

---

## Outstanding Balances View (FR-6.5)

For the Accountant dashboard:

```php
// In ReportingService or dedicated query
Invoice::with('booking.guest')
    ->whereIn('status', ['unpaid', 'partial'])
    ->orderByDesc('issued_at')
    ->get()
    ->map(function ($invoice) {
        $paid = $invoice->payments->sum('amount');
        return [
            'invoice'     => $invoice,
            'paid'        => $paid,
            'outstanding' => $invoice->total - $paid,
        ];
    });
```
