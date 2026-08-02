<x-app-layout>
    <x-slot name="title">Invoice INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('invoices.index') }}" class="hover:text-primary">Invoices</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.download', $invoice) }}" class="btn-outline btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Invoice Document Card (FR-6.1, FR-6.2) -->
        <div class="card md:col-span-2">
            <!-- Header Banner -->
            <div class="flex justify-between items-start pb-6 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-extrabold text-primary">{{ config('hms.hotel_name') }}</h2>
                    <p class="text-xs text-gray-500">{{ config('hms.hotel_address') }} &middot; {{ config('hms.hotel_phone') }}</p>
                </div>
                <div class="text-right">
                    <span class="font-mono text-xs text-gray-400 font-bold block">INVOICE</span>
                    <h3 class="text-xl font-mono font-extrabold text-primary">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</h3>
                    <span class="mt-1 badge badge-{{ $invoice->status }} uppercase">{{ $invoice->status }}</span>
                </div>
            </div>

            <!-- Billed To / Booking Ref -->
            <div class="grid grid-cols-2 gap-4 py-4 text-xs border-b border-gray-100">
                <div>
                    <p class="text-gray-400 font-semibold uppercase">Billed To:</p>
                    <p class="font-bold text-primary text-sm mt-0.5">{{ $invoice->booking->guest->name }}</p>
                    <p class="text-gray-500 font-mono">{{ $invoice->booking->guest->phone }}</p>
                    <p class="text-gray-500">{{ $invoice->booking->guest->id_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-400 font-semibold uppercase">Booking Reference:</p>
                    <p class="font-mono font-bold text-primary text-sm mt-0.5">{{ $invoice->booking->booking_reference }}</p>
                    <p class="text-gray-500">Room {{ $invoice->booking->room->room_number }} ({{ $invoice->booking->room->roomType->name }})</p>
                    <p class="text-gray-500">{{ $invoice->booking->check_in_date->format('M j') }} &ndash; {{ $invoice->booking->check_out_date->format('M j, Y') }} ({{ $breakdown['nights'] }} nights)</p>
                </div>
            </div>

            <!-- Itemized Line Items -->
            <div class="py-4">
                <table class="table text-xs">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-center">Qty / Nights</th>
                            <th class="text-right">Rate</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Room Charge ({{ $invoice->booking->room->roomType->name }} &ndash; Room {{ $invoice->booking->room->room_number }})</td>
                            <td class="text-center">{{ $breakdown['nights'] }}</td>
                            <td class="text-right">{{ config('hms.currency_symbol') }} {{ number_format($breakdown['rate'], 2) }}</td>
                            <td class="text-right font-semibold">{{ config('hms.currency_symbol') }} {{ number_format($breakdown['roomCharge'], 2) }}</td>
                        </tr>
                        @foreach($invoice->booking->additionalServices as $svc)
                        <tr>
                            <td>{{ $svc->name }} <span class="text-gray-400">({{ $svc->added_at->format('M j, Y') }})</span></td>
                            <td class="text-center">1</td>
                            <td class="text-right">{{ config('hms.currency_symbol') }} {{ number_format($svc->amount, 2) }}</td>
                            <td class="text-right font-semibold">{{ config('hms.currency_symbol') }} {{ number_format($svc->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Summary -->
            <div class="pt-4 border-t border-gray-100 flex justify-end text-xs">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span class="font-semibold">{{ config('hms.currency_symbol') }} {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax (0%):</span>
                        <span>{{ config('hms.currency_symbol') }} {{ number_format($invoice->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-extrabold text-primary pt-2 border-t border-gray-100">
                        <span>Total Due:</span>
                        <span>{{ config('hms.currency_symbol') }} {{ number_format($invoice->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-green-700 font-semibold">
                        <span>Amount Paid:</span>
                        <span>{{ config('hms.currency_symbol') }} {{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-red-600 font-extrabold text-sm pt-1 border-t border-gray-100">
                        <span>Outstanding Balance:</span>
                        <span>{{ config('hms.currency_symbol') }} {{ number_format($invoice->outstanding, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Processing & History Card (FR-6.3) -->
        <div class="card md:col-span-1">
            <h3 class="font-bold text-primary mb-3 pb-2 border-b border-gray-100">Record Payment</h3>

            @if($invoice->outstanding > 0)
            @role('admin|manager|accountant')
            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="space-y-3 mb-6">
                @csrf
                <div>
                    <label for="amount" class="form-label text-xs">Payment Amount (Max: {{ config('hms.currency_symbol') }} {{ number_format($invoice->outstanding, 2) }})</label>
                    <input type="number" step="0.01" min="0.01" max="{{ $invoice->outstanding }}" name="amount" id="amount" value="{{ old('amount', $invoice->outstanding) }}" required class="form-input text-xs">
                    @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="method" class="form-label text-xs">Payment Method</label>
                    <select name="method" id="method" required class="form-select text-xs">
                        <option value="cash">Cash</option>
                        <option value="card">Card / POS</option>
                        <option value="mobile_money">Mobile Money (MoMo)</option>
                    </select>
                    @error('method') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="form-label text-xs">Payment Reference / Notes</label>
                    <input type="text" name="notes" id="notes" placeholder="Receipt ref, MoMo tx ID..." class="form-input text-xs">
                </div>

                <button type="submit" class="btn-accent btn-sm w-full justify-center">Record Payment</button>
            </form>
            @endrole
            @else
            <div class="p-3 bg-green-50 text-green-800 rounded-lg text-xs font-semibold mb-6 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Invoice fully paid. Balance is zero.
            </div>
            @endif

            <!-- Payment Transactions History -->
            <h4 class="font-bold text-gray-700 text-xs mb-2">Payment Receipts History</h4>
            <div class="space-y-2 text-xs">
                @forelse($invoice->payments as $p)
                <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-primary capitalize">{{ str_replace('_', ' ', $p->method) }}</span>
                        <p class="text-gray-400 text-micro">{{ $p->paid_at->format('M j, Y g:i A') }}</p>
                        @if($p->notes) <p class="text-gray-500 text-micro italic">{{ $p->notes }}</p> @endif
                    </div>
                    <span class="font-extrabold text-green-700">+{{ config('hms.currency_symbol') }} {{ number_format($p->amount, 2) }}</span>
                </div>
                @empty
                <p class="text-gray-400 text-xs italic">No payments recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
