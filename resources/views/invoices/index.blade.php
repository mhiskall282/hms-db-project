<x-app-layout>
    <x-slot name="title">Invoices Directory</x-slot>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Invoices & Billing</h2>
            <p class="text-xs text-gray-500">View generated invoices, track payment status, and process collections.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex gap-4">
            <div>
                <select name="status" onchange="this.form.submit()" class="form-select text-xs">
                    <option value="">All Payment Statuses</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            @if(request('status'))
            <a href="{{ route('invoices.index') }}" class="btn-ghost btn-sm">Clear Filter</a>
            @endif
        </form>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Booking Ref</th>
                        <th>Guest Name</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Issued Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="font-mono font-bold text-primary">INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-mono text-xs font-semibold text-gray-600">{{ $inv->booking->booking_reference }}</td>
                        <td class="font-medium text-gray-900">{{ $inv->booking->guest->name }}</td>
                        <td class="font-semibold text-gray-900">{{ config('hms.currency_symbol') }} {{ number_format($inv->total, 2) }}</td>
                        <td class="text-green-700 font-medium">{{ config('hms.currency_symbol') }} {{ number_format($inv->amount_paid, 2) }}</td>
                        <td class="text-red-600 font-bold">{{ config('hms.currency_symbol') }} {{ number_format($inv->outstanding, 2) }}</td>
                        <td><span class="badge badge-{{ $inv->status }} uppercase text-xs">{{ $inv->status }}</span></td>
                        <td class="text-xs text-gray-500">{{ $inv->issued_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('invoices.show', $inv) }}" class="btn-ghost btn-sm">View</a>
                                <a href="{{ route('invoices.download', $inv) }}" class="text-xs text-primary font-semibold hover:underline px-2">PDF</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-500">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $invoices->withQueryString()->links() }}
    </div>
</x-app-layout>
