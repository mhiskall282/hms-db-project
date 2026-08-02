<x-app-layout>
    <x-slot name="title">Create Staff Account</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('users.index') }}" class="hover:text-primary">Staff Accounts</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Create</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Create Staff User Account</h2>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="form-label">Staff Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="e.g. Ama Mensah" class="form-input">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email Address (Login Username) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="staff@hms.local" class="form-input">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="form-label">Assigned Role <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="form-select">
                        <option value="">Select Role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="form-label">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required class="form-input">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="form-input">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-accent">
                    <label for="is_active" class="text-xs text-gray-700 font-medium">Account is Active</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Create Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
