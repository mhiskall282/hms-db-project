<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Room;
use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(): View
    {
        $roomTypes = RoomType::withCount('rooms')->orderBy('name')->get();
        return view('room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        return view('room-types.create');
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        RoomType::create($request->validated());
        return redirect()->route('room-types.index')->with('success', 'Room type created successfully.');
    }

    public function show(RoomType $roomType): View
    {
        $roomType->load('rooms');
        return view('room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType): View
    {
        return view('room-types.edit', compact('roomType'));
    }

    public function update(UpdateRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $roomType->update($request->validated());
        return redirect()->route('room-types.index')->with('success', 'Room type updated successfully.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        if ($roomType->rooms()->exists()) {
            return back()->with('error', 'Cannot delete a room type that has rooms assigned to it.');
        }
        $roomType->delete();
        return redirect()->route('room-types.index')->with('success', 'Room type deleted.');
    }
}
