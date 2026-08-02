<x-app-layout>
    <x-slot name="title">Staff User Management</x-slot>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Staff Accounts & RBAC Roles</h2>
            <p class="text-xs text-gray-500">Manage system users, assign operational roles, and handle deactivations (FR-1.3).</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Create Staff Account
        </a>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Email / Login</th>
                        <th>Assigned Role</th>
                        <th>Account Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="font-semibold text-primary">{{ $u->name }}</td>
                        <td class="font-mono text-xs">{{ $u->email }}</td>
                        <td>
                            <span class="badge bg-purple-50 text-purple-700 border-purple-200 capitalize">
                                {{ $u->getRoleNames()->first() ?? 'No Role' }}
                            </span>
                        </td>
                        <td>
                            @if($u->is_active)
                            <span class="badge badge-available">Active</span>
                            @else
                            <span class="badge badge-cancelled">Deactivated</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('users.edit', $u) }}" class="btn-ghost btn-sm">Edit</a>
                                @if($u->id !== auth()->id())
                                    @if($u->is_active)
                                    <form method="POST" action="{{ route('users.deactivate', $u) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-amber-600 hover:text-amber-800 text-xs font-semibold px-2 py-1 hover:bg-amber-50 rounded">Deactivate</button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('users.activate', $u) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-semibold px-2 py-1 hover:bg-green-50 rounded">Activate</button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">No staff accounts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-app-layout>
