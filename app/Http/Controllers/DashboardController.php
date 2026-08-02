<?php

namespace App\Http\Controllers;

use App\Services\ReportingService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ReportingService $reporting) {}

    public function index(Request $request)
    {
        $metrics = $this->reporting->getDashboardMetrics();

        return view('dashboard', compact('metrics'));
    }
}
