<x-app-layout>
    <x-slot name="title">Room Types</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Room Configuration & Rates</h2>
            <p class="text-xs text-gray-500">Manage room categories, base rates, and capacity limits.</p>
        </div>
        <a href="{{ route('room-types.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Room Type
        </a>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Base Rate / Night</th>
                        <th>Max Capacity</th>
                        <th>Assigned Rooms</th>
                        <th>Description</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roomTypes as $type)
                    <tr>
                        <td class="font-semibold text-primary">{{ $type->name }}</td>
                        <td class="font-medium text-gray-900">{{ config('hms.currency_symbol') }} {{ number_format($type->base_rate, 2) }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1 text-xs text-gray-600 font-medium">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $type->capacity }} {{ Str::plural('Guest', $type->capacity) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-blue-50 text-blue-700 border-blue-200">
                                {{ $type->rooms_count }} Rooms
                            </span>
                        </td>
                        <td class="text-xs text-gray-500 max-w-xs truncate">{{ $type->description ?? '—' }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('room-types.edit', $type) }}" class="btn-ghost btn-sm">Edit</a>
                                <form method="POST" action="{{ route('room-types.destroy', $type) }}" onsubmit="return confirm('Delete this room type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded hover:bg-red-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">No room types found. Click "Add Room Type" to create one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
