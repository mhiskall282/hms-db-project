<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousekeepingController extends Controller
{
    /**
     * FR-2.3 — Housekeeping: view rooms needing attention.
     */
    public function index(): View
    {
        $dirtyRooms       = Room::with('roomType')->where('status', 'dirty')->orderBy('room_number')->get();
        $maintenanceRooms = Room::with('roomType')->where('status', 'maintenance')->orderBy('room_number')->get();
        $allRooms         = Room::with('roomType')->orderBy('room_number')->get();

        return view('housekeeping.index', compact('dirtyRooms', 'maintenanceRooms', 'allRooms'));
    }

    /**
     * FR-2.3 — Update room status (housekeeping action).
     */
    public function updateStatus(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:available,maintenance,dirty'],
        ]);

        // Housekeeping can only set: available (cleaned), dirty, or flag for maintenance
        // Cannot set occupied or reserved — those are booking-driven
        $room->update(['status' => $validated['status']]);

        $statusLabel = ucfirst($validated['status']);
        return back()->with('success', "Room {$room->room_number} marked as {$statusLabel}.");
    }
}
