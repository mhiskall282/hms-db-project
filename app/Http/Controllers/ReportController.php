<?php

namespace App\Http\Controllers;

use App\Services\ReportingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OccupancyReportExport;
use App\Exports\RevenueReportExport;

class ReportController extends Controller
{
    public function __construct(private ReportingService $reporting) {}

    /**
     * FR-7.1 — Occupancy report.
     */
    public function occupancy(Request $request): View
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()))->startOfDay();
        $to   = Carbon::parse($request->get('to', now()))->endOfDay();

        $data = $this->reporting->getOccupancyReport($from, $to);

        return view('reports.occupancy', compact('data', 'from', 'to'));
    }

    /**
     * FR-7.2 — Revenue report.
     */
    public function revenue(Request $request): View
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()))->startOfDay();
        $to   = Carbon::parse($request->get('to', now()))->endOfDay();

        $data = $this->reporting->getRevenueReport($from, $to);

        return view('reports.revenue', compact('data', 'from', 'to'));
    }

    /**
     * FR-6.5 / FR-7.x — Outstanding balances.
     */
    public function outstanding(): View
    {
        $data = $this->reporting->getOutstandingBalances();
        return view('reports.outstanding', compact('data'));
    }

    /**
     * FR-7.4 — Export occupancy as PDF.
     */
    public function exportOccupancyPdf(Request $request)
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()))->startOfDay();
        $to   = Carbon::parse($request->get('to', now()))->endOfDay();
        $data = $this->reporting->getOccupancyReport($from, $to);
        $hotel = config('hms.hotel_name', 'Grand Hotel HMS');

        $pdf = Pdf::loadView('reports.occupancy-pdf', compact('data', 'from', 'to', 'hotel'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("occupancy-report-{$from->toDateString()}-to-{$to->toDateString()}.pdf");
    }

    /**
     * FR-7.4 — Export occupancy as CSV.
     */
    public function exportOccupancyCsv(Request $request)
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()))->startOfDay();
        $to   = Carbon::parse($request->get('to', now()))->endOfDay();
        $data = $this->reporting->getOccupancyReport($from, $to);

        return Excel::download(
            new OccupancyReportExport($data),
            "occupancy-report-{$from->toDateString()}-to-{$to->toDateString()}.csv"
        );
    }

    /**
     * FR-7.4 — Export revenue as PDF.
     */
    public function exportRevenuePdf(Request $request)
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()))->startOfDay();
        $to   = Carbon::parse($request->get('to', now()))->endOfDay();
        $data = $this->reporting->getRevenueReport($from, $to);
        $hotel = config('hms.hotel_name', 'Grand Hotel HMS');

        $pdf = Pdf::loadView('reports.revenue-pdf', compact('data', 'from', 'to', 'hotel'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("revenue-report-{$from->toDateString()}-to-{$to->toDateString()}.pdf");
    }

    /**
     * FR-7.4 — Export revenue as CSV.
     */
    public function exportRevenueCsv(Request $request)
    {
        $from = Carbon::parse($request->get('from', now()->startOfMonth()))->startOfDay();
        $to   = Carbon::parse($request->get('to', now()))->endOfDay();
        $data = $this->reporting->getRevenueReport($from, $to);

        return Excel::download(
            new RevenueReportExport($data),
            "revenue-report-{$from->toDateString()}-to-{$to->toDateString()}.csv"
        );
    }
}
