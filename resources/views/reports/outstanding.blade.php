<x-app-layout>
    <x-slot name="title">Outstanding Balances Report</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Outstanding Balances & Unpaid Accounts</h2>
            <p class="text-xs text-gray-500">Track unpaid invoices and pending guest collections (FR-6.5).</p>
        </div>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Booking Ref</th>
                        <th>Guest Name</th>
                        <th>Contact Phone</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Outstanding Balance</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td class="font-mono font-bold text-primary">INV-{{ str_pad($item['invoice']->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-mono text-xs font-semibold text-gray-600">{{ $item['booking_ref'] }}</td>
                        <td class="font-medium text-gray-900">{{ $item['guest']->name }}</td>
                        <td class="font-mono text-xs text-gray-600">{{ $item['guest']->phone }}</td>
                        <td class="font-semibold text-gray-900">{{ config('hms.currency_symbol') }} {{ number_format($item['invoice']->total, 2) }}</td>
                        <td class="text-green-700 font-medium">{{ config('hms.currency_symbol') }} {{ number_format($item['paid'], 2) }}</td>
                        <td class="text-red-600 font-extrabold">{{ config('hms.currency_symbol') }} {{ number_format($item['outstanding'], 2) }}</td>
                        <td><span class="badge badge-{{ $item['invoice']->status }} uppercase text-xs">{{ $item['invoice']->status }}</span></td>
                        <td class="text-right">
                            <a href="{{ route('invoices.show', $item['invoice']) }}" class="btn-primary btn-sm">Process Payment →</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-500">🎉 Great news! There are currently no outstanding unpaid balances.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
