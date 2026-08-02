<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Experience world-class luxury, comfort, and hospitality at Grand Hotel. Reserve luxury suites and rooms online.">
    <title>{{ config('hms.hotel_name', 'Grand Hotel HMS') }} — Luxury Accommodations & Reservations</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 selection:bg-accent selection:text-primary">

    <!-- Sticky Navigation Bar -->
    <header x-data="{ mobileMenuOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-primary/95 backdrop-blur-md border-b border-white/10 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center text-primary font-black text-xl shadow-lg shadow-accent/20 group-hover:scale-105 transition-transform">
                    H
                </div>
                <div>
                    <span class="text-white font-extrabold text-lg tracking-tight block group-hover:text-accent transition-colors">{{ config('hms.hotel_name') }}</span>
                    <span class="text-xs text-accent font-semibold tracking-widest uppercase">Luxury & Resilience</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#about" class="hover:text-accent transition-colors">About Us</a>
                <a href="#booking-search" class="hover:text-accent transition-colors">Find Rooms</a>
                <a href="#suites" class="hover:text-accent transition-colors">Suites</a>
                <a href="#amenities" class="hover:text-accent transition-colors">Amenities</a>
                <a href="{{ route('portal.lookup') }}" class="hover:text-accent transition-colors">Guest Portal</a>
                <a href="#contact" class="hover:text-accent transition-colors">Contact</a>
            </nav>

            <!-- Actions (Desktop & Mobile) -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-accent text-xs px-4 py-2">
                            Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs text-slate-300 hover:text-white font-semibold px-3 py-2 border border-white/20 rounded-lg hover:border-accent transition">
                            Staff Login
                        </a>
                        <a href="#booking-search" class="btn-accent text-xs px-4 py-2 shadow-lg shadow-accent/20">
                            Book Now
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle Navigation Menu" class="md:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 focus:outline-none transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Responsive Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" x-transition.origin.top class="md:hidden bg-slate-900/95 border-b border-white/10 px-4 pt-3 pb-6 space-y-3 backdrop-blur-xl" x-cloak>
            <a href="#about" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-200 hover:text-accent border-b border-white/5">About Us</a>
            <a href="#booking-search" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-200 hover:text-accent border-b border-white/5">Find Rooms & Reserve</a>
            <a href="#suites" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-200 hover:text-accent border-b border-white/5">Luxury Suites</a>
            <a href="#amenities" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-200 hover:text-accent border-b border-white/5">Hotel Amenities</a>
            <a href="{{ route('portal.lookup') }}" class="block py-2 text-sm font-semibold text-accent hover:underline border-b border-white/5">🔍 Guest Self-Service Portal</a>
            <a href="#contact" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-200 hover:text-accent border-b border-white/5">Contact Concierge</a>

            <div class="pt-3 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-accent text-xs justify-center py-2.5">
                        Staff Dashboard &rarr;
                    </a>
                @else
                    <a href="#booking-search" @click="mobileMenuOpen = false" class="btn-accent text-xs justify-center py-2.5 shadow-lg shadow-accent/20">
                        Book Room Now
                    </a>
                    <a href="{{ route('login') }}" class="text-xs text-center text-slate-300 font-semibold py-2.5 border border-white/20 rounded-xl hover:border-accent">
                        Staff Portal Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative min-h-screen pt-28 pb-16 flex items-center justify-center overflow-hidden">
        <!-- Background Hero Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero.png') }}" alt="Grand Hotel Exterior" class="w-full h-full object-cover object-center scale-105 animate-pulse-slow">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/80 to-primary/70"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-8">
            <!-- Luxury Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-accent/20 border border-accent/40 text-accent text-xs font-bold tracking-widest uppercase mb-6 backdrop-blur-sm animate-fade-in">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                5-Star Luxury Hospitality & Hospitality Excellence
            </div>

            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white tracking-tight leading-none mb-6">
                Where Timeless Elegance<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent via-amber-300 to-amber-500">Meets Modern Comfort</span>
            </h1>

            <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-300 font-normal leading-relaxed mb-10">
                Experience unparalleled hospitality, luxury suites, fine dining, and serene relaxation at {{ config('hms.hotel_name') }}. Reserve your stay today.
            </p>

            <!-- Floating Availability Search Form (FR-4.1 Public) -->
            <div id="booking-search" class="max-w-5xl mx-auto bg-slate-900/90 backdrop-blur-xl border border-white/15 rounded-2xl p-6 sm:p-8 shadow-2xl text-left">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search Room Availability & Reserve Online
                    </h2>
                    <span class="text-xs text-accent font-semibold">Instant Reservation Confirmation</span>
                </div>

                <form method="GET" action="{{ route('landing') }}#booking-search" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="check_in" class="block text-xs font-semibold text-slate-300 mb-1.5">Check-In Date</label>
                        <input type="date" name="check_in" id="check_in" value="{{ $searchParams['check_in'] }}" min="{{ today()->toDateString() }}" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>

                    <div>
                        <label for="check_out" class="block text-xs font-semibold text-slate-300 mb-1.5">Check-Out Date</label>
                        <input type="date" name="check_out" id="check_out" value="{{ $searchParams['check_out'] }}" min="{{ today()->addDays(1)->toDateString() }}" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>

                    <div>
                        <label for="room_type_id" class="block text-xs font-semibold text-slate-300 mb-1.5">Suite Category</label>
                        <select name="room_type_id" id="room_type_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:ring-2 focus:ring-accent focus:border-accent">
                            <option value="">All Suite Categories</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ $searchParams['room_type_id'] == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ config('hms.currency_symbol') }} {{ number_format($type->base_rate, 2) }}/night)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full btn-accent font-bold py-2.5 justify-center shadow-lg shadow-accent/20">
                            Check Availability
                        </button>
                    </div>
                </form>

                <!-- Available Rooms Results Grid -->
                @if(request()->filled(['check_in', 'check_out']))
                <div class="mt-8 pt-6 border-t border-white/10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-white text-sm">
                            Available Rooms ({{ \Carbon\Carbon::parse($searchParams['check_in'])->format('M j') }} &ndash; {{ \Carbon\Carbon::parse($searchParams['check_out'])->format('M j, Y') }})
                        </h3>
                        <span class="badge bg-green-500/20 text-green-400 border-green-500/40 text-xs font-semibold">{{ $availableRooms->count() }} Available</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($availableRooms as $room)
                        <div x-data="{ openReserve: false }" class="p-4 rounded-xl bg-slate-800/80 border border-slate-700 hover:border-accent transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="text-xs text-accent font-semibold">Floor {{ $room->floor }}</span>
                                    <h4 class="text-lg font-extrabold text-white">Room {{ $room->room_number }} — {{ $room->roomType->name }}</h4>
                                </div>
                                <span class="text-accent font-bold text-base">{{ config('hms.currency_symbol') }} {{ number_format($room->roomType->base_rate, 2) }} <span class="text-xs text-slate-400 font-normal">/ night</span></span>
                            </div>

                            <p class="text-xs text-slate-400 mb-4 line-clamp-2">{{ $room->roomType->description ?? 'Luxurious furnishings, high-speed Wi-Fi, air conditioning, and premium toiletries included.' }}</p>

                            <button type="button" @click="openReserve = true" class="w-full btn-accent text-xs justify-center py-2">
                                Reserve Room {{ $room->room_number }} Now
                            </button>

                            <!-- Reservation Modal -->
                            <div x-show="openReserve" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak>
                                <div class="bg-slate-900 border border-white/20 rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left" @click.away="openReserve = false">
                                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
                                        <h4 class="font-extrabold text-white text-base">Complete Guest Reservation</h4>
                                        <button type="button" @click="openReserve = false" class="text-slate-400 hover:text-white">&times;</button>
                                    </div>

                                    <form method="POST" action="{{ route('public.reserve') }}" class="space-y-3 text-xs">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $room->id }}">
                                        <input type="hidden" name="check_in_date" value="{{ $searchParams['check_in'] }}">
                                        <input type="hidden" name="check_out_date" value="{{ $searchParams['check_out'] }}">

                                        <div class="p-3 bg-slate-800 rounded-lg mb-3">
                                            <p class="font-bold text-accent text-sm">Room {{ $room->room_number }} ({{ $room->roomType->name }})</p>
                                            <p class="text-slate-300 mt-0.5">
                                                Dates: {{ \Carbon\Carbon::parse($searchParams['check_in'])->format('M j') }} to {{ \Carbon\Carbon::parse($searchParams['check_out'])->format('M j, Y') }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block font-medium text-slate-300 mb-1">Full Name <span class="text-red-400">*</span></label>
                                            <input type="text" name="name" required placeholder="e.g. Kwame Asante" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-medium text-slate-300 mb-1">Phone Number <span class="text-red-400">*</span></label>
                                                <input type="text" name="phone" required placeholder="+233 24 123 4567" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                            </div>
                                            <div>
                                                <label class="block font-medium text-slate-300 mb-1">Email Address</label>
                                                <input type="email" name="email" placeholder="guest@example.com" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-medium text-slate-300 mb-1">ID / Passport Number <span class="text-red-400">*</span></label>
                                                <input type="text" name="id_number" required placeholder="GHA-0001234" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                            </div>
                                            <div>
                                                <label class="block font-medium text-slate-300 mb-1">Nationality <span class="text-red-400">*</span></label>
                                                <input type="text" name="nationality" value="Ghanaian" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block font-medium text-slate-300 mb-1">Special Requests</label>
                                            <textarea name="notes" rows="2" placeholder="Late arrival, high floor, extra pillows..." class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white resize-none"></textarea>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                                            <button type="button" @click="openReserve = false" class="btn-ghost text-xs">Cancel</button>
                                            <button type="submit" class="btn-accent text-xs">Confirm Reservation</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-6 text-center text-slate-400 text-xs">
                            No rooms available for the selected dates. Please adjust your dates or room category.
                        </div>
                        @endforelse
                    </div>
                </div>
                @endif

                <!-- Booking Confirmation Banner -->
                @if(session('booking_success'))
                <div class="mt-6 p-4 rounded-xl bg-green-500/20 border border-green-500/40 text-green-300 text-xs space-y-1">
                    <p class="font-bold text-sm text-green-200">🎉 Reservation Confirmed!</p>
                    <p>Booking Reference: <strong class="font-mono text-white text-sm">{{ session('booking_success.reference') }}</strong></p>
                    <p>Guest: {{ session('booking_success.guest_name') }} &middot; Room {{ session('booking_success.room') }} ({{ session('booking_success.type') }})</p>
                    <p>Dates: {{ session('booking_success.check_in') }} to {{ session('booking_success.check_out') }} ({{ session('booking_success.nights') }} nights)</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mt-6 p-4 rounded-xl bg-red-500/20 border border-red-500/40 text-red-300 text-xs">
                    {{ session('error') }}
                </div>
                @endif

                @if(session('success'))
                <div class="mt-6 p-4 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-300 text-xs">
                    {{ session('success') }}
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-slate-950 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-accent text-xs font-bold uppercase tracking-widest">About Our Hotel</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2 mb-6">
                        Excellence & Comfort Redefined in Accra
                    </h2>
                    <p class="text-slate-400 leading-relaxed mb-4 text-sm sm:text-base">
                        Situated in the serene heart of Accra, {{ config('hms.hotel_name') }} offers world-class hospitality tailored for business travelers, vacationing families, and luxury seekers alike.
                    </p>
                    <p class="text-slate-400 leading-relaxed mb-6 text-sm sm:text-base">
                        Featuring meticulously designed suites, high-speed connectivity, 24-hour room service, and gourmet dining, every aspect of your stay is curated for absolute perfection.
                    </p>

                    <div class="grid grid-cols-3 gap-6 pt-4 border-t border-white/10">
                        <div>
                            <p class="text-3xl font-extrabold text-accent">50+</p>
                            <p class="text-xs text-slate-400">Luxury Rooms</p>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-accent">99.8%</p>
                            <p class="text-xs text-slate-400">Guest Satisfaction</p>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-accent">24/7</p>
                            <p class="text-xs text-slate-400">Concierge Service</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-2xl border border-white/10 group">
                        <img src="{{ asset('images/suite.png') }}" alt="Grand Hotel Luxury Suite" class="w-full h-96 object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Suites Showcase -->
    <section id="suites" class="py-20 bg-slate-900 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-accent text-xs font-bold uppercase tracking-widest">Suites & Accommodations</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Curated Luxury Suites</h2>
                <p class="text-slate-400 text-sm mt-3">Choose from our selection of premium suites designed for comfort and tranquility.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($roomTypes as $type)
                <div class="bg-slate-950 border border-white/10 rounded-2xl overflow-hidden hover:border-accent/50 transition duration-300 flex flex-col">
                    <div class="h-48 bg-slate-800 relative overflow-hidden">
                        <img src="{{ asset('images/suite.png') }}" alt="{{ $type->name }}" class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md px-3 py-1 rounded-full text-accent font-bold text-xs border border-accent/30">
                            {{ config('hms.currency_symbol') }} {{ number_format($type->base_rate, 2) }} / night
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2">{{ $type->name }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed mb-4">{{ $type->description ?? 'Includes high-speed internet, king bed, air conditioning, daily room service, and complimentary breakfast.' }}</p>
                        </div>

                        <div>
                            <div class="flex items-center gap-4 text-xs text-slate-300 mb-6 py-2 border-y border-white/5">
                                <span>👥 Max Capacity: <strong>{{ $type->capacity }} Guests</strong></span>
                                <span>🛏️ Rooms: <strong>{{ $type->rooms_count }} Units</strong></span>
                            </div>

                            <a href="#booking-search" class="w-full btn-accent text-xs justify-center py-2.5">
                                Reserve {{ $type->name }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Hotel Amenities Showcase -->
    <section id="amenities" class="py-20 bg-slate-950 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-accent text-xs font-bold uppercase tracking-widest">World-Class Experience</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Hotel Amenities & Services</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Pool Card -->
                <div class="relative rounded-2xl overflow-hidden border border-white/10 group h-80">
                    <img src="{{ asset('images/pool.png') }}" alt="Rooftop Infinity Pool" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent p-6 flex flex-col justify-end">
                        <h3 class="text-2xl font-bold text-white">Rooftop Infinity Pool</h3>
                        <p class="text-slate-300 text-xs mt-1">Enjoy panoramic skyline views while relaxing in our temperature-controlled infinity pool.</p>
                    </div>
                </div>

                <!-- Dining Card -->
                <div class="relative rounded-2xl overflow-hidden border border-white/10 group h-80">
                    <img src="{{ asset('images/dining.png') }}" alt="Gourmet Fine Dining" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent p-6 flex flex-col justify-end">
                        <h3 class="text-2xl font-bold text-white">Gourmet Fine Dining</h3>
                        <p class="text-slate-300 text-xs mt-1">Savor exquisite local and international cuisine prepared by master culinary chefs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Reservation Enquiries -->
    <section id="contact" class="py-20 bg-slate-900 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <span class="text-accent text-xs font-bold uppercase tracking-widest">Get In Touch</span>
                    <h2 class="text-3xl font-extrabold text-white mt-2 mb-6">Contact Concierge & Reservations</h2>
                    <p class="text-slate-400 text-sm mb-8">Have questions about your upcoming trip, special event hosting, or corporate bookings? Reach out to our 24/7 front desk team.</p>

                    <div class="space-y-4 text-sm text-slate-300">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 border border-accent/30 rounded-xl flex items-center justify-center text-accent">📍</div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold">Address</p>
                                <p class="font-medium text-white">{{ config('hms.hotel_address') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 border border-accent/30 rounded-xl flex items-center justify-center text-accent">📞</div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold">Phone</p>
                                <p class="font-medium text-white">{{ config('hms.hotel_phone') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 border border-accent/30 rounded-xl flex items-center justify-center text-accent">✉️</div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold">Email</p>
                                <p class="font-medium text-white">{{ config('hms.hotel_email') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-slate-950 border border-white/10 rounded-2xl p-6 sm:p-8">
                    <h3 class="text-xl font-bold text-white mb-4">Send Us a Message</h3>

                    <form method="POST" action="{{ route('public.contact') }}" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label for="c_name" class="block text-slate-300 font-medium mb-1">Your Full Name</label>
                            <input type="text" name="name" id="c_name" required placeholder="Kwame Asante" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        </div>

                        <div>
                            <label for="c_email" class="block text-slate-300 font-medium mb-1">Your Email Address</label>
                            <input type="email" name="email" id="c_email" required placeholder="kwame@example.com" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        </div>

                        <div>
                            <label for="c_subject" class="block text-slate-300 font-medium mb-1">Subject</label>
                            <input type="text" name="subject" id="c_subject" required placeholder="Room Inquiry / Event Request" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        </div>

                        <div>
                            <label for="c_message" class="block text-slate-300 font-medium mb-1">Message</label>
                            <textarea name="message" id="c_message" rows="4" required placeholder="Tell us how we can assist you..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white resize-none"></textarea>
                        </div>

                        <button type="submit" class="w-full btn-accent font-bold py-3 justify-center text-xs">
                            Send Message &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-white/10 py-12 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-white font-bold text-sm">{{ config('hms.hotel_name') }}</p>
                <p>&copy; {{ date('Y') }} {{ config('hms.hotel_name') }}. All rights reserved.</p>
            </div>

            <div class="flex items-center gap-6">
                <a href="#about" class="hover:text-white">About</a>
                <a href="#suites" class="hover:text-white">Suites</a>
                <a href="#contact" class="hover:text-white">Contact</a>
                <a href="{{ route('login') }}" class="text-accent hover:underline font-semibold">Staff Portal Login</a>
            </div>
        </div>
    </footer>

</body>
</html>
