<x-app-layout>
    <x-slot name="title">Booking {{ $booking->booking_reference }}</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('bookings.index') }}" class="hover:text-primary">Bookings</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">{{ $booking->booking_reference }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($booking->status === 'confirmed' && $booking->check_in_date->isToday())
            <form method="POST" action="{{ route('bookings.check-in', $booking) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-accent">✓ Process Check-In</button>
            </form>
            @elseif($booking->status === 'checked_in')
            <form method="POST" action="{{ route('bookings.check-out', $booking) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-primary">✓ Process Check-Out</button>
            </form>
            @endif

            @if(in_array($booking->status, ['pending', 'confirmed']))
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-danger btn-sm">Cancel Booking</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Booking Overview -->
        <div class="card md:col-span-2">
            <div class="flex justify-between items-start mb-4 pb-3 border-b border-gray-100">
                <div>
                    <span class="text-xs font-mono font-bold text-gray-400">BOOKING REFERENCE</span>
                    <h2 class="text-2xl font-extrabold text-primary">{{ $booking->booking_reference }}</h2>
                </div>
                <span class="badge badge-{{ $booking->status }} capitalize text-sm py-1 px-3">
                    {{ str_replace('_', ' ', $booking->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-400 font-semibold uppercase">Guest</p>
                    <p class="font-bold text-primary text-base mt-0.5">
                        <a href="{{ route('guests.show', $booking->guest) }}" class="hover:underline">{{ $booking->guest->name }}</a>
                    </p>
                    <p class="text-xs text-gray-500 font-mono">{{ $booking->guest->phone }} &middot; {{ $booking->guest->id_number }}</p>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-400 font-semibold uppercase">Assigned Room</p>
                    <p class="font-bold text-primary text-base mt-0.5">Room {{ $booking->room->room_number }}</p>
                    <p class="text-xs text-gray-500">{{ $booking->room->roomType->name }} &middot; {{ config('hms.currency_symbol') }} {{ number_format($booking->room->roomType->base_rate, 2) }}/night</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-xs mb-6 p-4 bg-primary/5 rounded-xl">
                <div>
                    <p class="text-gray-500 font-semibold uppercase">Check-In</p>
                    <p class="font-bold text-primary text-sm mt-0.5">{{ $booking->check_in_date->format('D, M j, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold uppercase">Check-Out</p>
                    <p class="font-bold text-primary text-sm mt-0.5">{{ $booking->check_out_date->format('D, M j, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold uppercase">Duration</p>
                    <p class="font-bold text-primary text-sm mt-0.5">{{ $booking->nights }} {{ Str::plural('Night', $booking->nights) }}</p>
                </div>
            </div>

            @if($booking->notes)
            <div class="mb-4 text-xs">
                <p class="font-semibold text-gray-700">Booking Notes:</p>
                <p class="text-gray-600 bg-gray-50 p-3 rounded-lg mt-1 italic">{{ $booking->notes }}</p>
            </div>
            @endif

            <!-- Additional Services List (FR-6.2) -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-primary text-sm">Additional Service Charges</h3>
                    @if($booking->status !== 'checked_out')
                    <button type="button" @click="$dispatch('open-service-modal')" class="text-xs text-accent font-semibold hover:underline">+ Add Service Charge</button>
                    @endif
                </div>

                <ul class="divide-y divide-gray-100 text-xs">
                    @forelse($booking->additionalServices as $svc)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-800">{{ $svc->name }}</span>
                            <span class="text-gray-400 ml-2">({{ $svc->added_at->format('M j, g:i A') }})</span>
                        </div>
                        <span class="font-semibold text-primary">{{ config('hms.currency_symbol') }} {{ number_format($svc->amount, 2) }}</span>
                    </li>
                    @empty
                    <li class="py-2 text-gray-400 italic">No additional services recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Sidebar Invoice Status Card -->
        <div class="card md:col-span-1">
            <h3 class="font-bold text-primary mb-3 pb-2 border-b border-gray-100">Billing & Invoice Summary</h3>

            @if($booking->invoice)
            <div class="space-y-3 text-xs mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">Invoice Status:</span>
                    <span class="badge badge-{{ $booking->invoice->status }} uppercase">{{ $booking->invoice->status }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Room Charge:</span>
                    <span class="font-medium">{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->room_charge, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Services Total:</span>
                    <span class="font-medium">{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->services_charge, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-sm text-primary pt-2 border-t border-gray-100">
                    <span>Total Amount:</span>
                    <span>{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->total, 2) }}</span>
                </div>
                <div class="flex justify-between text-green-700">
                    <span>Amount Paid:</span>
                    <span class="font-semibold">{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between text-red-600 font-bold text-sm pt-1 border-t border-gray-100">
                    <span>Outstanding:</span>
                    <span>{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->outstanding, 2) }}</span>
                </div>
            </div>

            <a href="{{ route('invoices.show', $booking->invoice) }}" class="btn-primary btn-sm w-full justify-center">View Invoice & Payments →</a>
            @else
            <p class="text-xs text-gray-500 mb-4">Invoice not yet generated. An invoice is automatically generated upon check-out or can be created now.</p>
            <form method="POST" action="{{ route('invoices.generate', $booking) }}">
                @csrf
                <button type="submit" class="btn-accent btn-sm w-full justify-center">Generate Invoice Now</button>
            </form>
            @endif
        </div>
    </div>

    <!-- Modal for Adding Service Charge -->
    <div x-data="{ open: false }" @open-service-modal.window="open = true" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6" @click.away="open = false">
            <h3 class="text-lg font-bold text-primary mb-4">Add Service Charge</h3>
            <form method="POST" action="{{ route('bookings.add-service', $booking) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="svc_name" class="form-label">Service Name / Description</label>
                    <input type="text" name="name" id="svc_name" required placeholder="e.g. Room Service, Laundry, Airport Transfer" class="form-input">
                </div>
                <div>
                    <label for="svc_amount" class="form-label">Amount (GHS)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="svc_amount" required placeholder="0.00" class="form-input">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="open = false" class="btn-ghost">Cancel</button>
                    <button type="submit" class="btn-primary">Add Charge</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
