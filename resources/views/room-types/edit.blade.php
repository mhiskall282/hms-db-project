<x-app-layout>
    <x-slot name="title">Edit Room Type</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('room-types.index') }}" class="hover:text-primary">Room Types</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Edit {{ $roomType->name }}</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Edit Room Type: {{ $roomType->name }}</h2>

            <form method="POST" action="{{ route('room-types.update', $roomType) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="form-label">Room Type Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $roomType->name) }}" required class="form-input">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="base_rate" class="form-label">Base Rate per Night (GHS) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="base_rate" id="base_rate" value="{{ old('base_rate', $roomType->base_rate) }}" required class="form-input">
                        @error('base_rate') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="capacity" class="form-label">Max Guest Capacity <span class="text-red-500">*</span></label>
                        <input type="number" min="1" max="20" name="capacity" id="capacity" value="{{ old('capacity', $roomType->capacity) }}" required class="form-input">
                        @error('capacity') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-textarea">{{ old('description', $roomType->description) }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('room-types.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Room Type</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
