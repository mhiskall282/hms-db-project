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
<body class="font-sans antialiased bg-surface-muted min-h-screen" :class="{ 'overflow-hidden lg:overflow-auto': sidebarOpen }" x-data="{ sidebarOpen: false, ...idleTimer() }" x-init="startTimer()" @mousemove="resetTimer()" @keydown.window="resetTimer()" @click="resetTimer()">

    <!-- Mobile Navigation Top Bar (Visible < lg screens) -->
    <div class="lg:hidden bg-primary text-white p-4 sticky top-0 z-30 flex justify-between items-center border-b border-primary-light shadow-md">
        <div class="flex items-center gap-3">
            <button type="button" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle Staff Navigation" class="p-1.5 rounded-lg bg-primary-light text-white focus:outline-none hover:bg-accent hover:text-primary transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="sidebarOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-accent rounded flex items-center justify-center text-primary font-black text-sm">H</div>
                <span class="font-bold text-sm tracking-tight text-white">{{ config('hms.hotel_name') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-accent font-semibold capitalize bg-primary-light px-2.5 py-1 rounded-full">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}</span>
            <a href="{{ route('profile.edit') }}" class="w-7 h-7 bg-accent text-primary rounded-full flex items-center justify-center text-xs font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </a>
        </div>
    </div>

    <!-- Mobile Dark Backdrop Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-slate-950/75 z-30 backdrop-blur-sm lg:hidden" x-cloak></div>

    <!-- Sidebar (Drawer on mobile & tablet < lg, fixed scrollable sidebar on desktop >= lg) -->
    <aside class="sidebar" :class="{ 'mobile-open': sidebarOpen }" id="sidebar">
        <!-- Header & Logo (Fixed at top) -->
        <div class="sidebar-logo flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-accent rounded-lg flex items-center justify-center text-primary font-bold text-lg shadow-sm">H</div>
                <div>
                    <p class="text-white font-bold text-sm leading-tight tracking-tight">{{ config('hms.hotel_name') }}</p>
                    <p class="text-[11px] text-accent font-medium">Management System</p>
                </div>
            </div>
            <!-- Close Button for Mobile & Tablet -->
            <button type="button" @click="sidebarOpen = false" aria-label="Close menu" class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-primary-light transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- User Info Badge (Fixed below logo) -->
        <div class="px-5 py-3 border-b border-primary-light/60 shrink-0 bg-primary-dark/40">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-accent/20 text-accent border border-accent/40 flex items-center justify-center font-bold text-xs shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="truncate">
                    <p class="text-white text-xs font-semibold truncate leading-tight">{{ auth()->user()->name }}</p>
                    <span class="inline-flex items-center text-[10px] text-accent font-medium capitalize bg-primary-light/80 px-2 py-0.5 rounded mt-0.5">
                        👑 {{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Scrollable Navigation Items -->
        <nav class="flex-1 py-3 px-2 overflow-y-auto space-y-1 custom-sidebar-scroll">
            <!-- Core Section -->
            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            @role('admin|manager|receptionist')
            <div class="pt-3 pb-1 px-3"><p class="text-gray-400/80 text-[10px] uppercase tracking-wider font-bold">Front Desk & Guests</p></div>
            <a href="{{ route('check-in-out.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('check-in-out.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                <span>Check-In / Out</span>
            </a>
            <a href="{{ route('bookings.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Bookings</span>
            </a>
            <a href="{{ route('guests.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('guests.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Guest Directory</span>
            </a>
            @endrole

            @role('admin|manager|receptionist|housekeeping')
            <div class="pt-3 pb-1 px-3"><p class="text-gray-400/80 text-[10px] uppercase tracking-wider font-bold">Rooms & Maintenance</p></div>
            @role('admin|manager')
            <a href="{{ route('room-types.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('room-types.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Room Categories</span>
            </a>
            @endrole
            <a href="{{ route('rooms.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span>Room Inventory</span>
            </a>
            @endrole

            @role('admin|manager|housekeeping')
            <a href="{{ route('housekeeping.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('housekeeping.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Housekeeping</span>
            </a>
            @endrole

            @role('admin|manager|housekeeping|receptionist')
            <a href="{{ route('maintenance.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                <span>Maintenance Tickets</span>
            </a>
            @endrole

            @role('admin|manager|receptionist')
            <a href="{{ route('reviews.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <span>Guest Reviews</span>
            </a>
            @endrole

            @role('admin|manager|accountant|receptionist')
            <div class="pt-3 pb-1 px-3"><p class="text-gray-400/80 text-[10px] uppercase tracking-wider font-bold">Billing & Invoices</p></div>
            <a href="{{ route('invoices.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Invoices & Payments</span>
            </a>
            @endrole

            @role('admin|manager')
            <div class="pt-3 pb-1 px-3"><p class="text-gray-400/80 text-[10px] uppercase tracking-wider font-bold">Reports & Security</p></div>
            <a href="{{ route('reports.occupancy') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('reports.occupancy*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Occupancy Report</span>
            </a>
            <a href="{{ route('reports.revenue') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('reports.revenue*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Revenue Report</span>
            </a>
            <a href="{{ route('audit-logs.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Audit Trail</span>
            </a>
            @endrole

            @role('admin|manager|accountant')
            <a href="{{ route('reports.outstanding') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('reports.outstanding*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Outstanding Balances</span>
            </a>
            @endrole

            @role('admin')
            <div class="pt-3 pb-1 px-3"><p class="text-gray-400/80 text-[10px] uppercase tracking-wider font-bold">System Administration</p></div>
            <a href="{{ route('users.index') }}" @click="sidebarOpen = false" class="sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Staff Accounts</span>
            </a>
            @endrole
        </nav>

        <!-- Logout Button (Fixed at bottom) -->
        <div class="border-t border-primary-light/60 p-3 shrink-0 bg-primary-dark/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-nav-item w-full text-left hover:bg-red-500/20 hover:text-red-300">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="content-area">
        <!-- Desktop Header (Hidden on Mobile) -->
        <header class="page-header sticky top-0 z-20 hidden lg:flex">
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
        <div class="px-4 sm:px-6 pt-4">
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
        <main class="page-content px-4 sm:px-6 py-6">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="px-4 sm:px-6 py-4 text-center text-xs text-gray-400 border-t border-hms-border bg-white">
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
