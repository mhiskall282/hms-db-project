<x-app-layout>
    <x-slot name="title">Edit Room {{ $room->room_number }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('rooms.index') }}" class="hover:text-primary">Rooms</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Edit Room {{ $room->room_number }}</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Edit Room {{ $room->room_number }}</h2>

            <form method="POST" action="{{ route('rooms.update', $room) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="room_number" class="form-label">Room Number <span class="text-red-500">*</span></label>
                        <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $room->room_number) }}" required class="form-input">
                        @error('room_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="floor" class="form-label">Floor Number <span class="text-red-500">*</span></label>
                        <input type="number" min="1" max="50" name="floor" id="floor" value="{{ old('floor', $room->floor) }}" required class="form-input">
                        @error('floor') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="room_type_id" class="form-label">Room Type <span class="text-red-500">*</span></label>
                        <select name="room_type_id" id="room_type_id" required class="form-select">
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ config('hms.currency_symbol') }} {{ number_format($type->base_rate, 2) }}/night)
                                </option>
                            @endforeach
                        </select>
                        @error('room_type_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="form-select">
                            @foreach(['available', 'occupied', 'reserved', 'dirty', 'maintenance'] as $st)
                                <option value="{{ $st }}" {{ old('status', $room->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('rooms.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Room</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
