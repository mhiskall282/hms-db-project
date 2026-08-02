<?php

namespace App\Http\Controllers;

use App\Actions\CreateBookingAction;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the public landing page with room types and availability search.
     */
    public function index(Request $request): View
    {
        $roomTypes = RoomType::withCount('rooms')->orderBy('base_rate', 'asc')->get();

        $searchParams = [
            'check_in'     => $request->get('check_in', today()->toDateString()),
            'check_out'    => $request->get('check_out', today()->addDays(1)->toDateString()),
            'room_type_id' => $request->get('room_type_id'),
        ];

        $availableRooms = collect();

        if ($request->filled(['check_in', 'check_out'])) {
            $from = $request->get('check_in');
            $to   = $request->get('check_out');

            $query = Room::where('status', 'available')
                ->whereDoesntHave('bookings', function ($bQuery) use ($from, $to) {
                    $bQuery->whereNotIn('status', ['cancelled'])
                        ->where('check_in_date', '<', $to)
                        ->where('check_out_date', '>', $from);
                });

            if ($request->filled('room_type_id')) {
                $query->where('room_type_id', $request->get('room_type_id'));
            }

            $availableRooms = $query->with('roomType')->get();
        }

        return view('welcome', compact('roomTypes', 'searchParams', 'availableRooms'));
    }

    /**
     * Handle public online reservation creation by a guest.
     */
    public function reserve(Request $request, CreateBookingAction $createBookingAction): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:30'],
            'email'          => ['nullable', 'email', 'max:255'],
            'id_number'      => ['required', 'string', 'max:50'],
            'nationality'    => ['required', 'string', 'max:100'],
            'room_id'        => ['required', 'exists:rooms,id'],
            'check_in_date'  => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        // Find system user or fallback to admin ID for public online bookings
        $systemUser = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first() ?? User::first();

        // Register or retrieve guest profile
        $guest = Guest::firstOrCreate(
            ['id_number' => $validated['id_number']],
            [
                'name'        => $validated['name'],
                'phone'       => $validated['phone'],
                'email'       => $validated['email'] ?? null,
                'nationality' => $validated['nationality'],
            ]
        );

        try {
            $booking = $createBookingAction->execute([
                'guest_id'       => $guest->id,
                'room_id'        => $validated['room_id'],
                'check_in_date'  => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'created_by'     => $systemUser->id,
                'notes'          => $validated['notes'] ?? 'Online public reservation request.',
            ]);

            return redirect()->to('/#booking-search')->with('booking_success', [
                'reference'  => $booking->booking_reference,
                'guest_name' => $guest->name,
                'room'       => $booking->room->room_number,
                'type'       => $booking->room->roomType->name,
                'check_in'   => $booking->check_in_date->format('M j, Y'),
                'check_out'  => $booking->check_out_date->format('M j, Y'),
                'nights'     => $booking->nights,
            ]);
        } catch (RoomNotAvailableException $e) {
            return redirect()->to('/#booking-search')->with('error', $e->getMessage());
        }
    }

    /**
     * Handle public contact form submission.
     */
    public function contact(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        return redirect()->to('/#contact')->with('success', 'Thank you for contacting us! Our concierge team will get back to you shortly.');
    }
}
