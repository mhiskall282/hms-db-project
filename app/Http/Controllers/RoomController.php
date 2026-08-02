<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::with('roomType')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->room_type_id, fn($q) => $q->where('room_type_id', $request->room_type_id))
            ->when($request->floor, fn($q) => $q->where('floor', $request->floor))
            ->orderBy('room_number')
            ->paginate(20);

        $roomTypes = RoomType::orderBy('name')->get();
        return view('rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::orderBy('name')->get();
        return view('rooms.create', compact('roomTypes'));
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function show(Room $room): View
    {
        $room->load(['roomType', 'bookings.guest', 'activeBooking.guest']);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $roomTypes = RoomType::orderBy('name')->get();
        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->bookings()->whereNotIn('status', ['cancelled', 'checked_out'])->exists()) {
            return back()->with('error', 'Cannot delete a room with active or pending bookings.');
        }
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted.');
    }
}
