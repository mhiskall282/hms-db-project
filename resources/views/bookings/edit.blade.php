<x-app-layout>
    <x-slot name="title">Edit Booking {{ $booking->booking_reference }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('bookings.index') }}" class="hover:text-primary">Bookings</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Edit {{ $booking->booking_reference }}</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Edit Booking {{ $booking->booking_reference }}</h2>

            <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Guest</label>
                    <input type="text" disabled value="{{ $booking->guest->name }} ({{ $booking->guest->phone }})" class="form-input bg-gray-100 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Guest assignment cannot be changed after creation.</p>
                </div>

                <div>
                    <label class="form-label">Assigned Room</label>
                    <input type="text" disabled value="Room {{ $booking->room->room_number }} — {{ $booking->room->roomType->name }}" class="form-input bg-gray-100 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Room assignment cannot be changed directly. Cancel and create a new booking to reassign.</p>
                </div>

                <div>
                    <label for="status" class="form-label">Booking Status</label>
                    <select name="status" id="status" class="form-select">
                        @foreach(['pending', 'confirmed', 'cancelled'] as $st)
                            <option value="{{ $st }}" {{ old('status', $booking->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notes" class="form-label">Special Notes / Requests</label>
                    <textarea name="notes" id="notes" rows="3" class="form-textarea">{{ old('notes', $booking->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('bookings.show', $booking) }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Booking</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
