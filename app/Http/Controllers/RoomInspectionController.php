<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Room;
use App\Models\RoomInspection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomInspectionController extends Controller
{
    public function create(Room $room): View
    {
        return view('inspections.create', compact('room'));
    }

    public function store(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'linen_changed'       => ['nullable', 'boolean'],
            'bathroom_sanitized'  => ['nullable', 'boolean'],
            'amenities_restocked' => ['nullable', 'boolean'],
            'appliances_checked'  => ['nullable', 'boolean'],
            'minibar_checked'     => ['nullable', 'boolean'],
            'notes'               => ['nullable', 'string', 'max:500'],
        ]);

        $inspection = RoomInspection::create([
            'room_id'             => $room->id,
            'inspector_id'        => auth()->id(),
            'linen_changed'       => $request->boolean('linen_changed'),
            'bathroom_sanitized'  => $request->boolean('bathroom_sanitized'),
            'amenities_restocked' => $request->boolean('amenities_restocked'),
            'appliances_checked'  => $request->boolean('appliances_checked'),
            'minibar_checked'     => $request->boolean('minibar_checked'),
            'notes'               => $validated['notes'] ?? null,
            'inspected_at'        => now(),
        ]);

        // If all 5 criteria pass, update room status to available!
        $allPassed = $inspection->linen_changed &&
                     $inspection->bathroom_sanitized &&
                     $inspection->amenities_restocked &&
                     $inspection->appliances_checked &&
                     $inspection->minibar_checked;

        if ($allPassed && $room->status === 'dirty') {
            $room->update(['status' => 'available']);
        }

        AuditLog::log('housekeeping', 'room.inspected', "Housekeeping inspected Room {$room->room_number} (Passed: " . ($allPassed ? 'YES' : 'NO') . ")");

        return redirect()->route('housekeeping.index')->with('success', "Room {$room->room_number} inspection recorded. Status: " . ucfirst($room->status));
    }
}
