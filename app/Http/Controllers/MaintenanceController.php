<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        $requests = MaintenanceRequest::with(['room', 'reporter', 'resolver'])->latest()->paginate(20);
        $rooms = Room::orderBy('room_number')->get();

        return view('maintenance.index', compact('requests', 'rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id'     => ['required', 'exists:rooms,id'],
            'issue_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
        ]);

        $room = Room::findOrFail($validated['room_id']);

        $mReq = MaintenanceRequest::create([
            'room_id'     => $validated['room_id'],
            'reported_by' => auth()->id(),
            'issue_title' => $validated['issue_title'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'],
            'status'      => 'open',
            'reported_at' => now(),
        ]);

        // Automatically set room to maintenance status
        $room->update(['status' => 'maintenance']);

        AuditLog::log('housekeeping', 'maintenance.created', "Maintenance issue '{$mReq->issue_title}' reported for Room {$room->room_number}");

        return redirect()->route('maintenance.index')->with('success', "Maintenance request reported for Room {$room->room_number}.");
    }

    public function resolve(Request $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $maintenanceRequest->update([
            'status'           => 'resolved',
            'resolved_by'      => auth()->id(),
            'resolved_at'      => now(),
            'resolution_notes' => $validated['resolution_notes'] ?? 'Issue resolved by maintenance staff.',
        ]);

        // Automatically set room back to available
        $maintenanceRequest->room->update(['status' => 'available']);

        AuditLog::log('housekeeping', 'maintenance.resolved', "Maintenance issue resolved for Room {$maintenanceRequest->room->room_number}");

        return redirect()->route('maintenance.index')->with('success', "Maintenance ticket for Room {$maintenanceRequest->room->room_number} resolved.");
    }
}
