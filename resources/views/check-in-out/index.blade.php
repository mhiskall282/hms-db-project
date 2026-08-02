<x-app-layout>
    <x-slot name="title">Check-In / Check-Out Desk</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Front Desk Operations</h2>
            <p class="text-xs text-gray-500">Today's expected guest arrivals and departures.</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 bg-accent/20 text-primary rounded-full">
            Today: {{ today()->format('D, M j, Y') }}
        </span>
    </div>

    <!-- Two-Column Layout: Arrivals vs Departures -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Column 1: Today's Arrivals (Check-In — FR-5.1) -->
        <div class="card border-t-4 border-t-blue-500">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </div>
                    <h3 class="font-bold text-primary">Today's Arrivals</h3>
                </div>
                <span class="badge badge-reserved">{{ $arrivals->count() }} Guests</span>
            </div>

            <div class="space-y-3">
                @forelse($arrivals as $booking)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="font-mono text-xs text-gray-400 font-semibold">{{ $booking->booking_reference }}</span>
                        <h4 class="font-bold text-primary text-sm">{{ $booking->guest->name }}</h4>
                        <p class="text-xs text-gray-500">Room {{ $booking->room->room_number }} &middot; {{ $booking->room->roomType->name }} &middot; {{ $booking->nights }} nights</p>
                    </div>

                    <form method="POST" action="{{ route('bookings.check-in', $booking) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-accent btn-sm">Process Check-In</button>
                    </form>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    No confirmed check-in arrivals scheduled for today.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Column 2: Today's Departures (Check-Out — FR-5.2) -->
        <div class="card border-t-4 border-t-purple-500">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <h3 class="font-bold text-primary">Today's Departures</h3>
                </div>
                <span class="badge badge-checked_in">{{ $departures->count() }} Guests</span>
            </div>

            <div class="space-y-3">
                @forelse($departures as $booking)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="font-mono text-xs text-gray-400 font-semibold">{{ $booking->booking_reference }}</span>
                        <h4 class="font-bold text-primary text-sm">{{ $booking->guest->name }}</h4>
                        <p class="text-xs text-gray-500">Room {{ $booking->room->room_number }} &middot; {{ $booking->room->roomType->name }}</p>
                        @if($booking->invoice)
                        <span class="inline-block mt-1 badge badge-{{ $booking->invoice->status }} text-micro uppercase">
                            Invoice {{ $booking->invoice->status }} (Bal: {{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->outstanding, 2) }})
                        </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('bookings.check-out', $booking) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-primary btn-sm">Process Check-Out</button>
                    </form>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    No active guests scheduled for departure today.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
