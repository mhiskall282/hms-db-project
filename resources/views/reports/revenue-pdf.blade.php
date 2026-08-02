<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1E293B; line-height: 1.4; }
        .header { border-bottom: 2px solid #0A2647; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #0A2647; }
        .subtitle { font-size: 11px; color: #64748b; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th { background-color: #0A2647; color: white; padding: 8px; text-align: left; font-size: 10px; }
        .table td { padding: 6px 8px; border-bottom: 1px solid #E2E8F0; font-size: 10px; }
        .total-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 20px; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #E2E8F0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $hotel }} — Financial Revenue Report</div>
        <div class="subtitle">Period: {{ $from->format('M j, Y') }} to {{ $to->format('M j, Y') }} &middot; Generated {{ now()->format('M j, Y g:i A') }}</div>
    </div>

    <div class="total-box">
        <table style="width: 100%;">
            <tr>
                <td><strong>Total Revenue:</strong> GHS {{ number_format($data['totalRevenue'], 2) }}</td>
                <td><strong>Room Revenue:</strong> GHS {{ number_format($data['roomRevenue'], 2) }}</td>
                <td><strong>Services Revenue:</strong> GHS {{ number_format($data['servicesRevenue'], 2) }}</td>
            </tr>
        </table>
    </div>

    <h4 style="color: #0A2647; margin-bottom: 5px;">Payment Channels Breakdown</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th style="text-align: right;">Amount Collected (GHS)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash Payments</td>
                <td style="text-align: right; font-weight: bold;">GHS {{ number_format($data['byMethod']['cash'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Card / POS Payments</td>
                <td style="text-align: right; font-weight: bold;">GHS {{ number_format($data['byMethod']['card'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Mobile Money (MoMo)</td>
                <td style="text-align: right; font-weight: bold;">GHS {{ number_format($data['byMethod']['mobile_money'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Confidential Financial Document &middot; {{ $hotel }} Management System
    </div>
</body>
</html>
