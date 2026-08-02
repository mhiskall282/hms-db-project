<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1E293B; line-height: 1.5; }
        .header { border-bottom: 2px solid #0A2647; padding-bottom: 10px; margin-bottom: 20px; }
        .hotel-name { font-size: 20px; font-weight: bold; color: #0A2647; }
        .invoice-title { font-size: 18px; font-weight: bold; color: #0A2647; text-align: right; }
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td { vertical-align: top; width: 50%; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background-color: #0A2647; color: white; padding: 8px; text-align: left; font-size: 11px; }
        .items-table td { padding: 8px; border-bottom: 1px solid #E2E8F0; font-size: 11px; }
        .totals-table { width: 40%; float: right; border-collapse: collapse; }
        .totals-table td { padding: 5px; font-size: 11px; }
        .totals-table tr.total-row td { font-weight: bold; font-size: 13px; border-top: 2px solid #0A2647; }
        .badge { font-weight: bold; text-transform: uppercase; padding: 3px 8px; border-radius: 4px; font-size: 10px; }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-partial { background-color: #dbeafe; color: #1e40af; }
        .badge-unpaid { background-color: #fee2e2; color: #991b1b; }
        .footer { margin-top: 60px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #E2E8F0; padding-top: 10px; }
    </style>
</head>
<body>
    <table style="width: 100%;" class="header">
        <tr>
            <td>
                <div class="hotel-name">{{ $hotel }}</div>
                <div style="font-size: 10px; color: #64748b;">{{ config('hms.hotel_address') }} &middot; {{ config('hms.hotel_phone') }}</div>
            </td>
            <td style="text-align: right;">
                <div class="invoice-title">INVOICE</div>
                <div style="font-family: monospace; font-size: 13px; font-weight: bold;">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div style="margin-top: 5px;">
                    <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <td>
                <strong>BILLED TO:</strong><br>
                {{ $invoice->booking->guest->name }}<br>
                Phone: {{ $invoice->booking->guest->phone }}<br>
                ID: {{ $invoice->booking->guest->id_number }}
            </td>
            <td style="text-align: right;">
                <strong>BOOKING DETAILS:</strong><br>
                Reference: {{ $invoice->booking->booking_reference }}<br>
                Room: {{ $invoice->booking->room->room_number }} ({{ $invoice->booking->room->roomType->name }})<br>
                Dates: {{ $invoice->booking->check_in_date->format('Y-m-d') }} to {{ $invoice->booking->check_out_date->format('Y-m-d') }} ({{ $invoice->booking->nights }} nights)
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th style="text-align: center;">Qty / Nights</th>
                <th style="text-align: right;">Rate (GHS)</th>
                <th style="text-align: right;">Amount (GHS)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Room Accommodation ({{ $invoice->booking->room->roomType->name }} &ndash; Room {{ $invoice->booking->room->room_number }})</td>
                <td style="text-align: center;">{{ $invoice->booking->nights }}</td>
                <td style="text-align: right;">{{ number_format($invoice->booking->room->roomType->base_rate, 2) }}</td>
                <td style="text-align: right;">{{ number_format($invoice->room_charge, 2) }}</td>
            </tr>
            @foreach($invoice->booking->additionalServices as $svc)
            <tr>
                <td>{{ $svc->name }} ({{ $svc->added_at->format('M j, Y') }})</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($svc->amount, 2) }}</td>
                <td style="text-align: right;">{{ number_format($svc->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td style="text-align: right;">GHS {{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax (0%):</td>
            <td style="text-align: right;">GHS {{ number_format($invoice->tax, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total Due:</td>
            <td style="text-align: right;">GHS {{ number_format($invoice->total, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #065f46;">Amount Paid:</td>
            <td style="text-align: right; color: #065f46; font-weight: bold;">GHS {{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        <tr style="font-weight: bold; color: #991b1b;">
            <td>Outstanding:</td>
            <td style="text-align: right;">GHS {{ number_format($invoice->outstanding, 2) }}</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <div class="footer">
        Thank you for choosing {{ $hotel }}! &middot; Official Computer-Generated Invoice
    </div>
</body>
</html>
