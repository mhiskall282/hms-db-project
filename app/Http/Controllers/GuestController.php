<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $guests = Guest::query()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->orderBy('name')
            ->paginate(20);

        return view('guests.index', compact('guests'));
    }

    public function create(): View
    {
        return view('guests.create');
    }

    public function store(StoreGuestRequest $request): RedirectResponse
    {
        $guest = Guest::create($request->validated());
        return redirect()->route('guests.show', $guest)->with('success', 'Guest registered successfully.');
    }

    public function show(Guest $guest): View
    {
        $bookings = $guest->bookings()
            ->with(['room.roomType', 'invoice'])
            ->orderByDesc('check_in_date')
            ->get();

        return view('guests.show', compact('guest', 'bookings'));
    }

    public function edit(Guest $guest): View
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(UpdateGuestRequest $request, Guest $guest): RedirectResponse
    {
        $guest->update($request->validated());
        return redirect()->route('guests.show', $guest)->with('success', 'Guest updated successfully.');
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $guest->delete();
        return redirect()->route('guests.index')->with('success', 'Guest record deleted.');
    }
}
