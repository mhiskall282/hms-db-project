---
name: reporting-dashboard
description: "Use this skill when building the manager dashboard, occupancy/revenue reports, or PDF/CSV export."
---

# Reporting & Dashboard Skill

## Purpose

This skill defines the exact metrics, queries, and export formats required for the HMS reporting module (FR-7.x). Read this before building any dashboard widget, report page, or export function.

---

## FR-7.3 — Dashboard Metrics (Required)

The manager/admin dashboard must display these metrics in real time (or near-real-time — no caching requirement for a student deploy):

| Metric | Description | Query Hint |
|--------|-------------|-----------|
| **Current Occupancy** | Number of rooms with status = occupied | `Room::where('status', 'occupied')->count()` |
| **Occupancy Rate** | `(occupied rooms / total rooms) × 100%` | Divide by `Room::count()` |
| **Today's Check-Ins** | Bookings with `check_in_date = today` and status = confirmed | `Booking::whereDate('check_in_date', today())->where('status', 'confirmed')->count()` |
| **Today's Check-Outs** | Bookings with `check_out_date = today` and status = checked_in | `Booking::whereDate('check_out_date', today())->where('status', 'checked_in')->count()` |
| **Revenue Today** | Sum of payments recorded today | `Payment::whereDate('paid_at', today())->sum('amount')` |
| **Revenue This Month** | Sum of payments for the current calendar month | `Payment::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount')` |
| **Outstanding Balances** | Sum of unpaid/partial invoice totals minus payments | Aggregate across `invoices` joined with `payments` |
| **Pending Check-Ins** | Bookings confirmed but not yet checked in with past check-in date | Overdue arrivals |

---

## FR-7.1 — Occupancy Report

### Parameters (User-Selectable Filters)
- Date range: `from_date` to `to_date`
- Room type filter (optional)

### Report Data

| Column | Calculation |
|--------|------------|
| Date | Each date in the range |
| Rooms Occupied | Count of rooms occupied on that date |
| Rooms Available | Total rooms − occupied |
| Occupancy Rate | Occupied / Total × 100% |

### Implementation Approach

```php
// ReportingService::getOccupancyReport($from, $to)
$dates = CarbonPeriod::create($from, $to);

return collect($dates)->map(function ($date) {
    $occupied = Booking::where('status', 'checked_in')
        ->whereDate('check_in_date', '<=', $date)
        ->whereDate('check_out_date', '>', $date)
        ->count();

    $total = Room::where('status', '!=', 'maintenance')->count();

    return [
        'date'            => $date->toDateString(),
        'occupied'        => $occupied,
        'available'       => $total - $occupied,
        'occupancy_rate'  => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
    ];
});
```

---

## FR-7.2 — Revenue Report

### Parameters
- Date range: `from_date` to `to_date`
- Breakdown: by room type (optional), by payment method (optional)

### Report Data

| Column | Calculation |
|--------|------------|
| Room Revenue | Sum of (base_rate × nights) for all checked-out bookings in period |
| Additional Services Revenue | Sum of additional_services.amount in period |
| Total Revenue | Room Revenue + Additional Services Revenue |
| Tax Collected | Sum of invoice.tax for paid invoices in period |
| Payments by Method | Cash / Card / Mobile Money breakdown |

### Implementation

```php
// ReportingService::getRevenueReport($from, $to)
$invoices = Invoice::with('payments', 'booking.room.roomType', 'booking.additionalServices')
    ->whereHas('payments', fn($q) => $q->whereBetween('paid_at', [$from, $to]))
    ->get();

$roomRevenue     = $invoices->sum(fn($inv) => $inv->booking->room->roomType->base_rate
                                * $inv->booking->nights());
$servicesRevenue = $invoices->sum(fn($inv) => $inv->booking->additionalServices->sum('amount'));
$taxCollected    = $invoices->sum('tax');

$byMethod = Payment::whereBetween('paid_at', [$from, $to])
    ->selectRaw('method, SUM(amount) as total')
    ->groupBy('method')
    ->pluck('total', 'method');
```

---

## Export Formats (FR-7.4)

### PDF Export — `barryvdh/laravel-dompdf`

```php
// ReportController::exportPdf($type, $from, $to)
$data = $this->reportingService->getReport($type, $from, $to);
$pdf  = Pdf::loadView("reports.{$type}-pdf", $data)
           ->setPaper('a4', 'landscape');
return $pdf->download("{$type}-report-{$from}-to-{$to}.pdf");
```

PDF report views (`resources/views/reports/occupancy-pdf.blade.php`, `revenue-pdf.blade.php`) must use:
- HMS header (hotel name, report type, date range, generation timestamp)
- A clean table layout (no Tailwind — dompdf uses inline CSS)
- Totals row at the bottom
- Page numbers in footer

### CSV Export — `maatwebsite/excel`

Create an Export class per report type:

```php
// app/Exports/OccupancyReportExport.php
class OccupancyReportExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $data) {}

    public function headings(): array
    {
        return ['Date', 'Rooms Occupied', 'Rooms Available', 'Occupancy Rate (%)'];
    }

    public function collection(): Collection
    {
        return $this->data->map(fn($row) => [
            $row['date'],
            $row['occupied'],
            $row['available'],
            $row['occupancy_rate'],
        ]);
    }
}

// Controller:
return Excel::download(new OccupancyReportExport($data), "occupancy-{$from}-{$to}.csv");
```

---

## Dashboard Layout Requirements

The dashboard (`/dashboard`) is the first page after login. Layout:

```
┌───────────────────────────────────────────────────────────────────┐
│  Row 1: Metric Cards (4 columns)                                  │
│  [Current Occupancy] [Today's Check-ins] [Today's Check-outs] [Revenue Today] │
├───────────────────────────────────────────────────────────────────┤
│  Row 2: Charts / Activity (2 columns)                             │
│  [Occupancy Trend — 7-day bar chart] [Recent Bookings — mini table] │
├───────────────────────────────────────────────────────────────────┤
│  Row 3: Alerts                                                    │
│  [Overdue check-ins] [Outstanding balances] [Dirty rooms needing cleaning] │
└───────────────────────────────────────────────────────────────────┘
```

Use the `x-metric-card` Blade component defined in `ui-branding.md`.

---

## Role Visibility Rules

| Dashboard Widget | Admin | Manager | Receptionist | Housekeeping | Accountant |
|-----------------|-------|---------|-------------|-------------|------------|
| Occupancy rate | ✅ | ✅ | ✅ (limited) | ❌ | ❌ |
| Today's check-ins/outs | ✅ | ✅ | ✅ | ❌ | ❌ |
| Revenue metrics | ✅ | ✅ | ❌ | ❌ | ✅ |
| Outstanding balances | ✅ | ✅ | ❌ | ❌ | ✅ |
| Dirty rooms widget | ✅ | ✅ | ✅ | ✅ | ❌ |
| Full reports page | ✅ | ✅ | ❌ | ❌ | ✅ (invoices only) |
| PDF/CSV export | ✅ | ✅ | ❌ | ❌ | ✅ (invoices only) |
