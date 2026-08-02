<x-app-layout>
    <x-slot name="title">Bookings Directory</x-slot>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Booking Management</h2>
            <p class="text-xs text-gray-500">Manage guest reservations, view status, and launch check-in workflows.</p>
        </div>
        <a href="{{ route('bookings.availability') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Search Availability & Book
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by booking reference (HMS-...) or guest name..." class="form-input text-xs">
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="form-select text-xs">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary btn-sm">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('bookings.index') }}" class="btn-ghost btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Nights</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="font-mono font-bold text-primary">
                            <a href="{{ route('bookings.show', $booking) }}" class="hover:underline">{{ $booking->booking_reference }}</a>
                        </td>
                        <td class="font-medium text-gray-900">{{ $booking->guest->name }}</td>
                        <td>Room {{ $booking->room->room_number }} <span class="text-gray-400 text-xs">({{ $booking->room->roomType->name }})</span></td>
                        <td class="text-xs">{{ $booking->check_in_date->format('M j, Y') }}</td>
                        <td class="text-xs">{{ $booking->check_out_date->format('M j, Y') }}</td>
                        <td class="text-xs font-semibold">{{ $booking->nights }}</td>
                        <td>
                            <span class="badge badge-{{ $booking->status }} capitalize">
                                {{ str_replace('_', ' ', $booking->status) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn-ghost btn-sm">View</a>
                                @if($booking->status === 'confirmed' && $booking->check_in_date->isToday())
                                <form method="POST" action="{{ route('bookings.check-in', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-accent btn-sm">Check-In</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">No bookings found. Use "Search Availability & Book" to create one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $bookings->withQueryString()->links() }}
    </div>
</x-app-layout>
