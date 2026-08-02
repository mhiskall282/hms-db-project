<?php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordPaymentAction
{
    /**
     * Record a payment against an invoice.
     * Validates that the payment does not exceed the outstanding balance.
     *
     * @param  Invoice  $invoice   The invoice being paid
     * @param  array    $data      Must contain: amount, method, paid_at, recorded_by, notes?
     * @return Payment
     * @throws ValidationException
     */
    public function execute(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            // Reload invoice with payments, locked for update
            $invoice = Invoice::with('payments')->lockForUpdate()->find($invoice->id);

            $amountPaid  = (float) $invoice->payments->sum('amount');
            $outstanding = round((float) $invoice->total - $amountPaid, 2);
            $payAmount   = round((float) $data['amount'], 2);

            if ($payAmount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount must be greater than zero.',
                ]);
            }

            if ($payAmount > $outstanding + 0.001) { // small float tolerance
                throw ValidationException::withMessages([
                    'amount' => "Payment of GHS {$payAmount} exceeds outstanding balance of GHS {$outstanding}.",
                ]);
            }

            $payment = Payment::create([
                'invoice_id'  => $invoice->id,
                'amount'      => $payAmount,
                'method'      => $data['method'],
                'paid_at'     => $data['paid_at'] ?? now(),
                'recorded_by' => $data['recorded_by'],
                'notes'       => $data['notes'] ?? null,
            ]);

            // Recalculate invoice status
            $invoice->refresh();
            $invoice->recalculateStatus();

            return $payment;
        });
    }
}
