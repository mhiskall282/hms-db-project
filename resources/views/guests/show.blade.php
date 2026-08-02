<x-app-layout>
    <x-slot name="title">Guest Profile: {{ $guest->name }}</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('guests.index') }}" class="hover:text-primary">Guests</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">{{ $guest->name }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('guests.edit', $guest) }}" class="btn-outline btn-sm">Edit Profile</a>
            <a href="{{ route('bookings.create', ['guest_id' => $guest->id]) }}" class="btn-accent btn-sm">+ New Booking</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="card md:col-span-1">
            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                <div class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center text-xl font-bold">
                    {{ substr($guest->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-primary">{{ $guest->name }}</h2>
                    <p class="text-xs text-gray-400">{{ $guest->nationality }}</p>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Phone Number</dt>
                    <dd class="font-mono text-gray-800 font-medium">{{ $guest->phone }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Email Address</dt>
                    <dd class="text-gray-800">{{ $guest->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">ID Number / Passport</dt>
                    <dd class="font-mono text-gray-800">{{ $guest->id_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Notes & Preferences</dt>
                    <dd class="text-xs text-gray-600 mt-1 italic">{{ $guest->notes ?? 'No special notes recorded.' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Member Since</dt>
                    <dd class="text-xs text-gray-600">{{ $guest->created_at->format('M j, Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Booking History Card (FR-3.3) -->
        <div class="card md:col-span-2">
            <h3 class="font-bold text-primary mb-4 pb-2 border-b border-gray-100">Booking History</h3>

            <div class="table-container">
                <table class="table text-xs">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Room</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $b)
                        <tr>
                            <td class="font-mono font-semibold">
                                <a href="{{ route('bookings.show', $b) }}" class="text-primary hover:underline">{{ $b->booking_reference }}</a>
                            </td>
                            <td>Room {{ $b->room->room_number }} <span class="text-gray-400">({{ $b->room->roomType->name }})</span></td>
                            <td>{{ $b->check_in_date->format('M j') }} &ndash; {{ $b->check_out_date->format('M j, Y') }}</td>
                            <td><span class="badge badge-{{ $b->status }} capitalize">{{ str_replace('_', ' ', $b->status) }}</span></td>
                            <td>
                                @if($b->invoice)
                                <a href="{{ route('invoices.show', $b->invoice) }}" class="badge badge-{{ $b->invoice->status }} uppercase hover:underline">
                                    {{ $b->invoice->status }}
                                </a>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400">No booking history recorded for this guest.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
