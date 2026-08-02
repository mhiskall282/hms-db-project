<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * FR-7.3 — Dashboard metrics.
     */
    public function getDashboardMetrics(): array
    {
        $totalRooms    = Room::count();
        $occupied      = Room::where('status', 'occupied')->count();
        $available     = Room::where('status', 'available')->count();
        $dirty         = Room::where('status', 'dirty')->count();
        $maintenance   = Room::where('status', 'maintenance')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 1) : 0;

        $todayCheckins  = Booking::whereDate('check_in_date', today())
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();
        $todayCheckouts = Booking::whereDate('check_out_date', today())
            ->where('status', 'checked_in')
            ->count();

        $revenueToday = Payment::whereDate('paid_at', today())->sum('amount');
        $revenueMonth = Payment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $outstandingBalance = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->sum(fn($inv) => $inv->outstanding);

        $pendingArrivals = Booking::where('status', 'confirmed')
            ->where('check_in_date', '<=', today())
            ->count();

        return compact(
            'totalRooms', 'occupied', 'available', 'dirty', 'maintenance',
            'occupancyRate', 'todayCheckins', 'todayCheckouts',
            'revenueToday', 'revenueMonth', 'outstandingBalance', 'pendingArrivals'
        );
    }

    /**
     * FR-7.1 — Occupancy report for a date range.
     */
    public function getOccupancyReport(Carbon $from, Carbon $to): Collection
    {
        $totalRooms = Room::where('status', '!=', 'maintenance')->count();
        $period     = $from->daysUntil($to->addDay());

        return collect($period)->map(function (Carbon $date) use ($totalRooms) {
            $occupied = Booking::whereNotIn('status', ['cancelled', 'pending'])
                ->where('check_in_date', '<=', $date->toDateString())
                ->where('check_out_date', '>', $date->toDateString())
                ->count();

            return [
                'date'           => $date->toDateString(),
                'occupied'       => $occupied,
                'available'      => max(0, $totalRooms - $occupied),
                'total'          => $totalRooms,
                'occupancy_rate' => $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 1) : 0,
            ];
        });
    }

    /**
     * FR-7.2 — Revenue report for a date range.
     */
    public function getRevenueReport(Carbon $from, Carbon $to): array
    {
        $payments = Payment::with('invoice.booking.room.roomType')
            ->whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()])
            ->get();

        $totalRevenue = $payments->sum('amount');

        $byMethod = $payments->groupBy('method')
            ->map(fn($group) => $group->sum('amount'))
            ->toArray();

        // Room-specific revenue from invoices in range
        $invoiceIds = $payments->pluck('invoice_id')->unique();
        $invoices   = Invoice::with(['booking.room.roomType', 'booking.additionalServices'])
            ->whereIn('id', $invoiceIds)
            ->get();

        $roomRevenue     = $invoices->sum(fn($inv) => (float) $inv->room_charge);
        $servicesRevenue = $invoices->sum(fn($inv) => (float) $inv->services_charge);
        $taxCollected    = $invoices->sum(fn($inv) => (float) $inv->tax);

        return [
            'from'             => $from->toDateString(),
            'to'               => $to->toDateString(),
            'totalRevenue'     => $totalRevenue,
            'roomRevenue'      => $roomRevenue,
            'servicesRevenue'  => $servicesRevenue,
            'taxCollected'     => $taxCollected,
            'byMethod'         => $byMethod,
            'paymentCount'     => $payments->count(),
        ];
    }

    /**
     * FR-6.5 — Outstanding balances list.
     */
    public function getOutstandingBalances(): Collection
    {
        return Invoice::with(['booking.guest', 'payments'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderByDesc('issued_at')
            ->get()
            ->map(function (Invoice $invoice) {
                return [
                    'invoice'       => $invoice,
                    'paid'          => $invoice->amount_paid,
                    'outstanding'   => $invoice->outstanding,
                    'guest'         => $invoice->booking->guest,
                    'booking_ref'   => $invoice->booking->booking_reference,
                    'days_overdue'  => max(0, now()->diffInDays($invoice->issued_at, false) * -1),
                ];
            });
    }
}
