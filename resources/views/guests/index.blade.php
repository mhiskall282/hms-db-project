<x-app-layout>
    <x-slot name="title">Guest Directory</x-slot>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Guest Registration Directory</h2>
            <p class="text-xs text-gray-500">Search and manage registered guests and view booking histories.</p>
        </div>
        <a href="{{ route('guests.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Register New Guest
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('guests.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search guest by name, phone number, email, or ID number..." class="form-input flex-1">
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search'))
            <a href="{{ route('guests.index') }}" class="btn-ghost">Clear</a>
            @endif
        </form>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>VIP Loyalty Tier</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>ID Number</th>
                        <th>Nationality</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                    <tr>
                        <td class="font-semibold text-primary">
                            <a href="{{ route('guests.show', $guest) }}" class="hover:underline">{{ $guest->name }}</a>
                        </td>
                        <td>
                            <span class="badge text-xs uppercase font-bold px-2 py-0.5
                                {{ $guest->loyalty_tier === 'Platinum VIP' ? 'bg-purple-500/20 text-purple-400 border-purple-500/40' : ($guest->loyalty_tier === 'Gold VIP' ? 'bg-amber-500/20 text-amber-400 border-amber-500/40' : ($guest->loyalty_tier === 'Silver Member' ? 'bg-slate-500/20 text-slate-300 border-slate-500/40' : 'bg-blue-500/20 text-blue-400 border-blue-500/40')) }}">
                                👑 {{ $guest->loyalty_tier }} ({{ $guest->discount_percentage }}% Off)
                            </span>
                        </td>
                        <td class="font-mono text-xs">{{ $guest->phone }}</td>
                        <td class="text-xs text-gray-600">{{ $guest->email ?? '—' }}</td>
                        <td class="font-mono text-xs">{{ $guest->id_number }}</td>
                        <td class="text-xs">{{ $guest->nationality }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('guests.show', $guest) }}" class="btn-ghost btn-sm">View Profile</a>
                                <a href="{{ route('guests.edit', $guest) }}" class="text-xs text-gray-500 hover:text-primary px-2 py-1">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">No guests found. Click "Register New Guest" to add one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $guests->withQueryString()->links() }}
    </div>
</x-app-layout>
