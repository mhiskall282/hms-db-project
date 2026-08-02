<x-app-layout>
    <x-slot name="title">Register Guest</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('guests.index') }}" class="hover:text-primary">Guests</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Register</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Register New Guest Profile</h2>

            <form method="POST" action="{{ route('guests.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="form-label">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="e.g. Kwame Asante" class="form-input">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="form-label">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="+233244123456" class="form-input">
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="guest@example.com" class="form-input">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="id_number" class="form-label">ID Number / Passport <span class="text-red-500">*</span></label>
                        <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" required placeholder="GHA-0001234" class="form-input">
                        @error('id_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nationality" class="form-label">Nationality <span class="text-red-500">*</span></label>
                        <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Ghanaian') }}" required class="form-input">
                        @error('nationality') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="form-label">Preferences / Notes</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Special requests, dietary preferences, or room requirements..." class="form-textarea">{{ old('notes') }}</textarea>
                    @error('notes') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('guests.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Register Guest</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
