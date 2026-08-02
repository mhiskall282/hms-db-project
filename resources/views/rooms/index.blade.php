<x-app-layout>
    <x-slot name="title">Room Inventory</x-slot>

    <!-- Filters & Actions Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Room Inventory Management</h2>
            <p class="text-xs text-gray-500">Monitor status, clean state, and floor location for all hotel rooms.</p>
        </div>
        @role('admin|manager')
        <a href="{{ route('rooms.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Room
        </a>
        @endrole
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6 p-4">
        <form method="GET" action="{{ route('rooms.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" id="status" onchange="this.form.submit()" class="form-select text-xs">
                    <option value="">All Statuses</option>
                    @foreach(['available', 'occupied', 'reserved', 'dirty', 'maintenance'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="room_type_id" class="block text-xs font-semibold text-gray-600 mb-1">Room Type</label>
                <select name="room_type_id" id="room_type_id" onchange="this.form.submit()" class="form-select text-xs">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="floor" class="block text-xs font-semibold text-gray-600 mb-1">Floor</label>
                <select name="floor" id="floor" onchange="this.form.submit()" class="form-select text-xs">
                    <option value="">All Floors</option>
                    @foreach([1, 2, 3, 4, 5] as $fl)
                        <option value="{{ $fl }}" {{ request('floor') == $fl ? 'selected' : '' }}>Floor {{ $fl }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <a href="{{ route('rooms.index') }}" class="btn-ghost text-xs w-full text-center">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Room Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @forelse($rooms as $room)
        <div class="card hover:shadow-md transition border-t-4 {{ match($room->status) {
            'available'   => 'border-t-green-500',
            'occupied'    => 'border-t-red-500',
            'reserved'    => 'border-t-blue-500',
            'dirty'       => 'border-t-amber-500',
            'maintenance' => 'border-t-gray-500',
            default       => 'border-t-gray-300',
        } }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <span class="text-xs font-semibold text-gray-400">Floor {{ $room->floor }}</span>
                    <h3 class="text-xl font-extrabold text-primary">Room {{ $room->room_number }}</h3>
                </div>
                <span class="badge badge-{{ $room->status }} capitalize">
                    {{ $room->status }}
                </span>
            </div>

            <div class="mt-2 text-xs space-y-1 text-gray-600">
                <p><span class="font-medium text-gray-800">Type:</span> {{ $room->roomType->name }}</p>
                <p><span class="font-medium text-gray-800">Rate:</span> {{ config('hms.currency_symbol') }} {{ number_format($room->roomType->base_rate, 2) }} / night</p>
                <p><span class="font-medium text-gray-800">Capacity:</span> {{ $room->roomType->capacity }} Guests</p>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('rooms.show', $room) }}" class="text-xs text-primary font-semibold hover:underline">View Details →</a>
                @role('admin|manager')
                <div class="flex items-center gap-1">
                    <a href="{{ route('rooms.edit', $room) }}" class="text-xs text-gray-500 hover:text-primary px-2 py-1">Edit</a>
                </div>
                @endrole
            </div>
        </div>
        @empty
        <div class="col-span-full card text-center py-12 text-gray-500">
            No rooms matching the selected filter criteria.
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $rooms->withQueryString()->links() }}
    </div>
</x-app-layout>
