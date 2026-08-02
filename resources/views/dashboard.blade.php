<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Occupancy Rate -->
        <div class="card bg-gradient-to-br from-primary to-primary-light text-white border-0">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-300 font-medium">Occupancy Rate</p>
                    <p class="text-3xl font-bold mt-1">{{ $metrics['occupancyRate'] }}%</p>
                    <p class="text-xs text-gray-300 mt-2">{{ $metrics['occupied'] }} / {{ $metrics['totalRooms'] }} rooms</p>
                </div>
                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="card bg-gradient-to-br from-accent to-accent-dark text-primary border-0">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-primary/70 font-medium">Today's Revenue</p>
                    <p class="text-3xl font-bold mt-1">{{ config('hms.currency_symbol') }} {{ number_format($metrics['revenueToday'], 0) }}</p>
                    <p class="text-xs text-primary/60 mt-2">Month: {{ config('hms.currency_symbol') }} {{ number_format($metrics['revenueMonth'], 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Today's Check-ins -->
        <div class="card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Expected Arrivals</p>
                    <p class="text-3xl font-bold mt-1 text-primary">{{ $metrics['todayCheckins'] }}</p>
                    <p class="text-xs text-gray-400 mt-2">Departures: {{ $metrics['todayCheckouts'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <!-- Outstanding -->
        <div class="card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Outstanding</p>
                    <p class="text-3xl font-bold mt-1 text-red-600">{{ config('hms.currency_symbol') }} {{ number_format($metrics['outstandingBalance'], 0) }}</p>
                    <p class="text-xs text-gray-400 mt-2">Pending arrivals: {{ $metrics['pendingArrivals'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Status Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Room Status Breakdown -->
        <div class="card">
            <div class="card-header">
                <h2 class="font-bold text-primary text-base">Room Status Breakdown</h2>
                <a href="{{ route('rooms.index') }}" class="text-xs text-accent hover:text-accent-dark font-medium">View All →</a>
            </div>
            <div class="space-y-3">
                @foreach([
                    ['Available',    $metrics['available'],    'bg-green-500',  $metrics['totalRooms']],
                    ['Occupied',     $metrics['occupied'],     'bg-red-500',    $metrics['totalRooms']],
                    ['Dirty',        $metrics['dirty'],        'bg-amber-500',  $metrics['totalRooms']],
                    ['Maintenance',  $metrics['maintenance'],  'bg-gray-500',   $metrics['totalRooms']],
                ] as [$label, $count, $color, $total])
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 font-medium">{{ $label }}</span>
                        <span class="font-semibold text-primary">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="{{ $color }} h-2 rounded-full transition-all"
                             style="width: {{ $total > 0 ? round(($count / $total) * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="font-bold text-primary text-base">Quick Actions</h2>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @can('create bookings')
                <a href="{{ route('bookings.availability') }}" class="flex flex-col items-center gap-2 p-4 bg-primary/5 hover:bg-primary/10 rounded-xl transition text-center">
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span class="text-xs font-medium text-primary">New Booking</span>
                </a>
                @endcan

                @role('admin|manager|receptionist')
                <a href="{{ route('check-in-out.index') }}" class="flex flex-col items-center gap-2 p-4 bg-accent/10 hover:bg-accent/20 rounded-xl transition text-center">
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-primary">Check-In/Out</span>
                </a>
                <a href="{{ route('guests.create') }}" class="flex flex-col items-center gap-2 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition text-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-blue-800">Register Guest</span>
                </a>
                @endrole

                @role('admin|manager|accountant')
                <a href="{{ route('reports.outstanding') }}" class="flex flex-col items-center gap-2 p-4 bg-red-50 hover:bg-red-100 rounded-xl transition text-center">
                    <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-red-800">Outstanding Bills</span>
                </a>
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
