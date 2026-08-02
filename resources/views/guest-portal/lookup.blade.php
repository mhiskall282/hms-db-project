<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guest Self-Service Portal — {{ config('hms.hotel_name', 'Grand Hotel HMS') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col justify-between">

    <!-- Header -->
    <header class="border-b border-white/10 py-6 bg-slate-900/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-accent rounded-xl flex items-center justify-center text-primary font-black text-lg">H</div>
                <span class="font-extrabold text-white text-base">{{ config('hms.hotel_name') }}</span>
            </a>
            <a href="/" class="text-xs text-accent hover:underline font-semibold">&larr; Back to Main Website</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto w-full px-4 py-12">
        <div class="bg-slate-900 border border-white/15 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-accent/20 border border-accent/40 rounded-2xl flex items-center justify-center text-accent mx-auto mb-3 text-xl">
                    🔍
                </div>
                <h1 class="text-2xl font-extrabold text-white">Guest Self-Service Portal</h1>
                <p class="text-xs text-slate-400 mt-1">Look up your reservation status, invoice breakdown, or download your receipt.</p>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/40 text-red-300 text-xs">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.search') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="booking_reference" class="block text-slate-300 font-semibold mb-1">Booking Reference Number <span class="text-red-400">*</span></label>
                    <input type="text" name="booking_reference" id="booking_reference" placeholder="e.g. HMS-A1B2C3D4" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white uppercase placeholder:normal-case font-mono">
                </div>

                <div>
                    <label for="contact" class="block text-slate-300 font-semibold mb-1">Phone Number or Email Address <span class="text-red-400">*</span></label>
                    <input type="text" name="contact" id="contact" placeholder="+233 24 123 4567 or guest@example.com" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                </div>

                <button type="submit" class="w-full btn-accent font-bold py-3 justify-center text-xs shadow-lg shadow-accent/20">
                    Find My Reservation &rarr;
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-xs text-slate-500 border-t border-white/5">
        &copy; {{ date('Y') }} {{ config('hms.hotel_name') }}. All rights reserved.
    </footer>

</body>
</html>
