<x-app-layout>
    <x-slot name="title">Housekeeping Dashboard</x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-primary">Housekeeping & Maintenance</h2>
            <p class="text-xs text-gray-500">Track dirty rooms needing cleaning and rooms under maintenance.</p>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card bg-amber-50 border-amber-200">
            <p class="text-xs font-semibold text-amber-700 uppercase">Dirty Rooms (Needs Cleaning)</p>
            <p class="text-3xl font-extrabold text-amber-800 mt-1">{{ $dirtyRooms->count() }}</p>
        </div>
        <div class="card bg-gray-50 border-gray-200">
            <p class="text-xs font-semibold text-gray-600 uppercase">Under Maintenance</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $maintenanceRooms->count() }}</p>
        </div>
        <div class="card bg-green-50 border-green-200">
            <p class="text-xs font-semibold text-green-700 uppercase">Clean & Available</p>
            <p class="text-3xl font-extrabold text-green-800 mt-1">{{ $allRooms->where('status', 'available')->count() }}</p>
        </div>
    </div>

    <!-- Rooms Needing Attention -->
    <div class="card mb-6">
        <h3 class="font-bold text-primary mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Rooms Needing Cleaning or Maintenance
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($dirtyRooms->concat($maintenanceRooms) as $room)
            <div class="p-4 rounded-xl border {{ $room->status === 'dirty' ? 'bg-amber-50/50 border-amber-200' : 'bg-gray-50 border-gray-200' }}">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-xs font-semibold text-gray-400">Floor {{ $room->floor }}</span>
                        <h4 class="text-lg font-bold text-primary">Room {{ $room->room_number }}</h4>
                    </div>
                    <span class="badge badge-{{ $room->status }} capitalize">{{ $room->status }}</span>
                </div>
                <p class="text-xs text-gray-600 mb-3">{{ $room->roomType->name }}</p>

                <!-- Quick Action Buttons for Housekeeping -->
                <div class="flex flex-col gap-2">
                    @if($room->status === 'dirty')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('rooms.status', $room) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="available">
                            <button type="submit" class="w-full btn-primary btn-sm justify-center bg-green-600 hover:bg-green-700 text-xs py-1.5">
                                ✓ Quick Clean
                            </button>
                        </form>

                        <a href="{{ route('rooms.inspect.create', $room->id) }}" class="btn-accent text-xs px-3 py-1.5 justify-center">
                            📋 5-Point Inspection
                        </a>
                    </div>
                    @elseif($room->status === 'maintenance')
                    <form method="POST" action="{{ route('rooms.status', $room) }}" class="w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="available">
                        <button type="submit" class="w-full btn-primary btn-sm justify-center text-xs py-1.5">
                            ✓ Maintenance Resolved
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-gray-500 text-sm">
                🎉 All rooms are clean and fully operational! No rooms currently require housekeeping attention.
            </div>
            @endforelse
        </div>
    </div>

    <!-- All Rooms Overview -->
    <div class="card p-0">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-primary">All Rooms Status Management</h3>
        </div>
        <div class="table-container">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Floor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allRooms as $room)
                    <tr>
                        <td class="font-bold text-primary">Room {{ $room->room_number }}</td>
                        <td>Floor {{ $room->floor }}</td>
                        <td>{{ $room->roomType->name }}</td>
                        <td><span class="badge badge-{{ $room->status }} capitalize">{{ $room->status }}</span></td>
                        <td>
                            @if(!in_array($room->status, ['occupied', 'reserved']))
                            <form method="POST" action="{{ route('rooms.status', $room) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="form-select text-xs py-1 px-2">
                                    <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="dirty" {{ $room->status === 'dirty' ? 'selected' : '' }}>Dirty</option>
                                    <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </form>
                            @else
                            <span class="text-gray-400 italic">Managed via booking</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
