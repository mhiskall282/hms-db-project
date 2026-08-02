<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservation #{{ $booking->booking_reference }} — {{ config('hms.hotel_name', 'Grand Hotel HMS') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen pb-12">

    <!-- Header -->
    <header class="border-b border-white/10 py-6 bg-slate-900/80 backdrop-blur-md mb-8">
        <div class="max-w-4xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-accent rounded-xl flex items-center justify-center text-primary font-black text-lg">H</div>
                <span class="font-extrabold text-white text-base">{{ config('hms.hotel_name') }}</span>
            </a>
            <a href="{{ route('portal.lookup') }}" class="text-xs text-accent hover:underline font-semibold">&larr; Search Another Reservation</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 space-y-6">
        <!-- Reservation Header Card -->
        <div class="bg-slate-900 border border-white/15 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="text-xs text-accent font-bold uppercase tracking-widest">Reservation Found</span>
                <h1 class="text-3xl font-extrabold text-white font-mono mt-1">#{{ $booking->booking_reference }}</h1>
                <p class="text-xs text-slate-400 mt-1">Guest: <strong class="text-white">{{ $booking->guest->name }}</strong> ({{ $booking->guest->nationality }})</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="badge badge-{{ $booking->status === 'confirmed' ? 'available' : ($booking->status === 'checked_in' ? 'occupied' : 'dirty') }} text-xs uppercase font-bold px-3 py-1.5">
                    {{ str_replace('_', ' ', $booking->status) }}
                </span>

                @if($booking->invoice)
                <a href="{{ route('portal.invoice.download', $booking->id) }}" class="btn-accent text-xs px-4 py-2">
                    📄 Download Official PDF Receipt
                </a>
                @endif
            </div>
        </div>

        <!-- Booking Details & Room Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-900 border border-white/15 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4 pb-2 border-b border-white/10">Room & Stay Details</h3>

                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Room Reserved:</dt>
                        <dd class="font-bold text-white">Room {{ $booking->room->room_number }} ({{ $booking->room->roomType->name }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Nightly Rate:</dt>
                        <dd class="font-bold text-accent">{{ config('hms.currency_symbol') }} {{ number_format($booking->room->roomType->base_rate, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Check-In Date:</dt>
                        <dd class="font-medium text-white">{{ $booking->check_in_date->format('F j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Check-Out Date:</dt>
                        <dd class="font-medium text-white">{{ $booking->check_out_date->format('F j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Length of Stay:</dt>
                        <dd class="font-bold text-white">{{ $booking->nights }} Night(s)</dd>
                    </div>
                </dl>
            </div>

            <!-- Billing & Financial Breakdown -->
            <div class="bg-slate-900 border border-white/15 rounded-2xl p-6">
                <h3 class="font-bold text-white text-sm mb-4 pb-2 border-b border-white/10">Billing & Payment Summary</h3>

                @if($booking->invoice)
                <dl class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Invoice Total:</dt>
                        <dd class="font-extrabold text-white text-sm">{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->total, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Total Paid:</dt>
                        <dd class="font-bold text-green-400">{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->paid_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Outstanding Balance:</dt>
                        <dd class="font-bold text-amber-400">{{ config('hms.currency_symbol') }} {{ number_format($booking->invoice->outstanding_balance, 2) }}</dd>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-white/10">
                        <dt class="text-slate-400">Payment Status:</dt>
                        <dd><span class="badge badge-{{ $booking->invoice->status === 'paid' ? 'available' : 'dirty' }} text-xs uppercase">{{ $booking->invoice->status }}</span></dd>
                    </div>
                </dl>
                @else
                <p class="text-xs text-slate-400 py-4 text-center">Invoice will be generated upon front-desk check-in/out.</p>
                @endif
            </div>
        </div>
    </main>

</body>
</html>
