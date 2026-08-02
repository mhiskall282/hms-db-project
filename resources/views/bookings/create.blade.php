<x-app-layout>
    <x-slot name="title">Create Booking</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('bookings.index') }}" class="hover:text-primary">Bookings</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">New Reservation</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Create New Room Reservation</h2>

            <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
                @csrf

                <!-- Guest Selection -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="guest_id" class="form-label mb-0">Select Guest <span class="text-red-500">*</span></label>
                        <a href="{{ route('guests.create') }}" class="text-xs text-accent font-semibold hover:underline">+ Register New Guest</a>
                    </div>
                    <select name="guest_id" id="guest_id" required class="form-select">
                        <option value="">Choose a registered guest...</option>
                        @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ old('guest_id', request('guest_id')) == $guest->id ? 'selected' : '' }}>
                                {{ $guest->name }} ({{ $guest->phone }} &middot; {{ $guest->id_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('guest_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Room Selection -->
                <div>
                    <label for="room_id" class="form-label">Select Room <span class="text-red-500">*</span></label>
                    <select name="room_id" id="room_id" required class="form-select">
                        <option value="">Choose a room...</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', request('room_id')) == $room->id ? 'selected' : '' }}>
                                Room {{ $room->room_number }} — {{ $room->roomType->name }} ({{ config('hms.currency_symbol') }} {{ number_format($room->roomType->base_rate, 2) }}/night)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="check_in_date" class="form-label">Check-In Date <span class="text-red-500">*</span></label>
                        <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date', request('check_in_date', today()->toDateString())) }}" min="{{ today()->toDateString() }}" required class="form-input text-xs">
                        @error('check_in_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="check_out_date" class="form-label">Check-Out Date <span class="text-red-500">*</span></label>
                        <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date', request('check_out_date', today()->addDays(1)->toDateString())) }}" min="{{ today()->addDays(1)->toDateString() }}" required class="form-input text-xs">
                        @error('check_out_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="form-label">Special Notes / Requests</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Late arrival, extra towels, ground floor request..." class="form-textarea">{{ old('notes') }}</textarea>
                    @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('bookings.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
