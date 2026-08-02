<x-app-layout>
    <x-slot name="title">Edit Staff Account {{ $user->name }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('users.index') }}" class="hover:text-primary">Staff Accounts</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 font-medium">Edit {{ $user->name }}</span>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-primary mb-4 pb-3 border-b border-gray-100">Edit Staff Account: {{ $user->name }}</h2>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="form-label">Staff Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="form-input">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="form-label">Assigned Role <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="form-select">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="p-3 bg-gray-50 rounded-lg space-y-3">
                    <p class="text-xs font-semibold text-gray-600">Change Password (Leave blank to keep current password)</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="form-label text-xs">New Password</label>
                            <input type="password" name="password" id="password" class="form-input text-xs">
                            @error('password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label text-xs">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input text-xs">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-accent">
                    <label for="is_active" class="text-xs text-gray-700 font-medium">Account is Active</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
