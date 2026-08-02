<x-app-layout>
    <x-slot name="title">Room Availability Search</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('bookings.index') }}" class="hover:text-primary">Bookings</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Availability Search</span>
        </div>

        <!-- Search Form (FR-4.1) -->
        <div class="card mb-6">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Search Available Rooms
            </h2>

            <form method="GET" action="{{ route('bookings.availability') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="check_in" class="form-label">Check-In Date <span class="text-red-500">*</span></label>
                    <input type="date" name="check_in" id="check_in" value="{{ request('check_in', today()->toDateString()) }}" min="{{ today()->toDateString() }}" required class="form-input text-xs">
                </div>

                <div>
                    <label for="check_out" class="form-label">Check-Out Date <span class="text-red-500">*</span></label>
                    <input type="date" name="check_out" id="check_out" value="{{ request('check_out', today()->addDays(1)->toDateString()) }}" min="{{ today()->addDays(1)->toDateString() }}" required class="form-input text-xs">
                </div>

                <div>
                    <label for="room_type_id" class="form-label">Room Type (Optional)</label>
                    <select name="room_type_id" id="room_type_id" class="form-select text-xs">
                        <option value="">All Room Types</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (Cap: {{ $type->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex justify-end">
                    <button type="submit" class="btn-accent px-6">Find Available Rooms</button>
                </div>
            </form>
        </div>

        <!-- Available Rooms Results -->
        @if(request()->filled(['check_in', 'check_out']))
        <div class="card">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <h3 class="font-bold text-primary">
                    Available Rooms ({{ $searchParams['from'] }} to {{ $searchParams['to'] }})
                </h3>
                <span class="badge badge-available">{{ $rooms->count() }} Rooms Found</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($rooms as $room)
                <div class="p-4 rounded-xl border border-gray-200 bg-white hover:border-accent hover:shadow-sm transition">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="text-xs font-semibold text-gray-400">Floor {{ $room->floor }}</span>
                            <h4 class="text-lg font-bold text-primary">Room {{ $room->room_number }}</h4>
                        </div>
                        <span class="badge badge-available">Available</span>
                    </div>

                    <div class="text-xs space-y-1 text-gray-600 mb-4">
                        <p><span class="font-semibold text-gray-800">Type:</span> {{ $room->roomType->name }}</p>
                        <p><span class="font-semibold text-gray-800">Rate:</span> {{ config('hms.currency_symbol') }} {{ number_format($room->roomType->base_rate, 2) }} / night</p>
                        <p><span class="font-semibold text-gray-800">Capacity:</span> {{ $room->roomType->capacity }} Guests</p>
                    </div>

                    <a href="{{ route('bookings.create', [
                        'room_id'        => $room->id,
                        'check_in_date'  => $searchParams['from'],
                        'check_out_date' => $searchParams['to'],
                    ]) }}" class="btn-primary btn-sm w-full justify-center">
                        Book Room {{ $room->room_number }} →
                    </a>
                </div>
                @empty
                <div class="col-span-full py-8 text-center text-gray-500 text-sm">
                    No rooms available for the selected date range. Try different dates or room type.
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
