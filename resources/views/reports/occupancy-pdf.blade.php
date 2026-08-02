<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Occupancy Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1E293B; line-height: 1.4; }
        .header { border-bottom: 2px solid #0A2647; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #0A2647; }
        .subtitle { font-size: 11px; color: #64748b; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th { background-color: #0A2647; color: white; padding: 8px; text-align: left; font-size: 10px; }
        .table td { padding: 6px 8px; border-bottom: 1px solid #E2E8F0; font-size: 10px; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #E2E8F0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $hotel }} — Occupancy Report</div>
        <div class="subtitle">Period: {{ $from->format('M j, Y') }} to {{ $to->format('M j, Y') }} &middot; Generated {{ now()->format('M j, Y g:i A') }}</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Occupied Rooms</th>
                <th>Available Rooms</th>
                <th>Total Operable Rooms</th>
                <th>Occupancy Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row['date'])->format('D, M j, Y') }}</td>
                <td style="color: #dc2626; font-weight: bold;">{{ $row['occupied'] }}</td>
                <td style="color: #16a34a; font-weight: bold;">{{ $row['available'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td style="font-weight: bold;">{{ $row['occupancy_rate'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Confidential Internal Document &middot; {{ $hotel }} Management System
    </div>
</body>
</html>
