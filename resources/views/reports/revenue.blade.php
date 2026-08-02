<x-app-layout>
    <x-slot name="title">Revenue Report</x-slot>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Financial Revenue Report</h2>
            <p class="text-xs text-gray-500">Track total revenue collected, room vs service breakdowns, and payment channels (FR-7.2).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.revenue.pdf', request()->all()) }}" class="btn-outline btn-sm">Export PDF</a>
            <a href="{{ route('reports.revenue.csv', request()->all()) }}" class="btn-accent btn-sm">Export CSV</a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('reports.revenue') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <label for="from" class="form-label text-xs">Start Date</label>
                <input type="date" name="from" id="from" value="{{ $from->toDateString() }}" class="form-input text-xs">
            </div>
            <div>
                <label for="to" class="form-label text-xs">End Date</label>
                <input type="date" name="to" id="to" value="{{ $to->toDateString() }}" class="form-input text-xs">
            </div>
            <button type="submit" class="btn-primary btn-sm">Generate Report</button>
        </form>
    </div>

    <!-- KPI Revenue Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="card bg-accent/10 border-accent/30">
            <p class="text-xs font-semibold text-primary uppercase">Total Revenue</p>
            <p class="text-2xl font-extrabold text-primary mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['totalRevenue'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $data['paymentCount'] }} Payment Transactions</p>
        </div>
        <div class="card">
            <p class="text-xs font-semibold text-gray-500 uppercase">Room Accommodation</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['roomRevenue'], 2) }}</p>
        </div>
        <div class="card">
            <p class="text-xs font-semibold text-gray-500 uppercase">Additional Services</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['servicesRevenue'], 2) }}</p>
        </div>
        <div class="card">
            <p class="text-xs font-semibold text-gray-500 uppercase">Tax Collected</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['taxCollected'], 2) }}</p>
        </div>
    </div>

    <!-- Payment Channels Breakdown -->
    <div class="card mb-6">
        <h3 class="font-bold text-primary mb-4 pb-2 border-b border-gray-100">Revenue by Payment Channel</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 font-semibold uppercase">Cash</p>
                <p class="text-xl font-bold text-primary mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['byMethod']['cash'] ?? 0, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 font-semibold uppercase">Card / POS</p>
                <p class="text-xl font-bold text-primary mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['byMethod']['card'] ?? 0, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-400 font-semibold uppercase">Mobile Money (MoMo)</p>
                <p class="text-xl font-bold text-primary mt-1">{{ config('hms.currency_symbol') }} {{ number_format($data['byMethod']['mobile_money'] ?? 0, 2) }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
