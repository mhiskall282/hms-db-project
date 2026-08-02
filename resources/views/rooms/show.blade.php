<x-app-layout>
    <x-slot name="title">Room {{ $room->room_number }} Details</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('rooms.index') }}" class="hover:text-primary">Rooms</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Room {{ $room->room_number }}</span>
        </div>
        @role('admin|manager')
        <a href="{{ route('rooms.edit', $room) }}" class="btn-outline btn-sm">Edit Room</a>
        @endrole
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Room Details Card -->
        <div class="card md:col-span-1">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <h2 class="text-2xl font-extrabold text-primary">Room {{ $room->room_number }}</h2>
                <span class="badge badge-{{ $room->status }} capitalize text-sm">{{ $room->status }}</span>
            </div>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Floor</dt>
                    <dd class="font-semibold text-gray-800">Floor {{ $room->floor }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Room Type</dt>
                    <dd class="font-semibold text-gray-800">{{ $room->roomType->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Base Nightly Rate</dt>
                    <dd class="font-semibold text-primary text-base">{{ config('hms.currency_symbol') }} {{ number_format($room->roomType->base_rate, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Max Capacity</dt>
                    <dd class="font-semibold text-gray-800">{{ $room->roomType->capacity }} Guests</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 font-medium uppercase">Description</dt>
                    <dd class="text-xs text-gray-600 mt-1">{{ $room->roomType->description ?? 'No description.' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Booking History for Room -->
        <div class="card md:col-span-2">
            <h3 class="font-bold text-primary mb-4 pb-2 border-b border-gray-100">Recent & Upcoming Bookings</h3>

            <div class="table-container">
                <table class="table text-xs">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Dates</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($room->bookings as $b)
                        <tr>
                            <td class="font-mono font-semibold">
                                <a href="{{ route('bookings.show', $b) }}" class="text-primary hover:underline">{{ $b->booking_reference }}</a>
                            </td>
                            <td class="font-medium text-gray-800">{{ $b->guest->name }}</td>
                            <td>{{ $b->check_in_date->format('M j') }} &ndash; {{ $b->check_out_date->format('M j, Y') }}</td>
                            <td><span class="badge badge-{{ $b->status }} capitalize">{{ str_replace('_', ' ', $b->status) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400">No booking history for this room.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
