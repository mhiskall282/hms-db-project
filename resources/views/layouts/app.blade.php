<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Hotel Management System — {{ config('hms.hotel_name') }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('hms.hotel_name', 'Hotel Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-surface-muted" x-data="idleTimer()" x-init="startTimer()" @mousemove="resetTimer()" @keydown.window="resetTimer()" @click="resetTimer()">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-accent rounded-lg flex items-center justify-center text-primary font-bold text-lg">H</div>
                <div>
                    <p class="text-white font-bold text-sm leading-tight">{{ config('hms.hotel_name') }}</p>
                    <p class="text-gray-400 text-xs">Management System</p>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="px-4 py-3 border-b border-primary-light">
            <p class="text-white text-sm font-medium">{{ auth()->user()->name }}</p>
            <p class="text-gray-400 text-xs capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}</p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            @role('admin|manager|receptionist')
            <a href="{{ route('check-in-out.index') }}" class="sidebar-nav-item {{ request()->routeIs('check-in-out.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Check-In / Out
            </a>
            <a href="{{ route('bookings.index') }}" class="sidebar-nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Bookings
            </a>
            <a href="{{ route('guests.index') }}" class="sidebar-nav-item {{ request()->routeIs('guests.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Guests
            </a>
            @endrole

            @role('admin|manager|receptionist|housekeeping')
            <a href="{{ route('rooms.index') }}" class="sidebar-nav-item {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                Rooms
            </a>
            @endrole

            @role('admin|manager|housekeeping')
            <a href="{{ route('housekeeping.index') }}" class="sidebar-nav-item {{ request()->routeIs('housekeeping.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Housekeeping
            </a>
            @endrole

            @role('admin|manager|accountant')
            <a href="{{ route('invoices.index') }}" class="sidebar-nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Invoices
            </a>
            @endrole

            @role('admin|manager')
            <div class="pt-4 pb-1 px-4"><p class="text-gray-500 text-xs uppercase tracking-widest font-semibold">Reports</p></div>
            <a href="{{ route('reports.occupancy') }}" class="sidebar-nav-item {{ request()->routeIs('reports.occupancy*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Occupancy
            </a>
            <a href="{{ route('reports.revenue') }}" class="sidebar-nav-item {{ request()->routeIs('reports.revenue*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Revenue
            </a>
            <div class="pt-4 pb-1 px-4"><p class="text-gray-500 text-xs uppercase tracking-widest font-semibold">Configuration</p></div>
            <a href="{{ route('room-types.index') }}" class="sidebar-nav-item {{ request()->routeIs('room-types.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Room Types
            </a>
            @endrole

            @role('admin|manager|accountant')
            <a href="{{ route('reports.outstanding') }}" class="sidebar-nav-item {{ request()->routeIs('reports.outstanding*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Outstanding
            </a>
            @endrole

            @role('admin')
            <a href="{{ route('users.index') }}" class="sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Staff Accounts
            </a>
            @endrole
        </nav>

        <!-- Logout -->
        <div class="border-t border-primary-light p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-nav-item w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- Page Header -->
        <header class="page-header sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <h1 class="page-title">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 hidden sm:block">{{ now()->format('D, M j Y') }}</span>
                <a href="{{ route('profile.edit') }}" class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold hover:bg-primary-dark transition">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ session('warning') }}
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="page-content">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="px-6 py-4 text-center text-xs text-gray-400 border-t border-hms-border bg-white">
            &copy; {{ date('Y') }} {{ config('hms.hotel_name') }} — HMS &middot; ICT Education Final Year Project
        </footer>
    </div>

    <!-- Idle Timer Script (FR-1.4) -->
    <script>
    function idleTimer() {
        return {
            timeout: null,
            idleMinutes: {{ config('session.lifetime', 30) }},
            startTimer() { this.resetTimer(); },
            resetTimer() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    window.location.href = '{{ route('logout.idle') }}';
                }, this.idleMinutes * 60 * 1000);
            }
        }
    }
    </script>
</body>
</html>
