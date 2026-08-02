<?php

namespace App\Http\Controllers;

use App\Actions\CreateBookingAction;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private AvailabilityService $availability,
        private CreateBookingAction $createBooking
    ) {}

    public function index(Request $request): View
    {
        $bookings = Booking::with(['guest', 'room.roomType'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('guest', fn($gq) => $gq->search($request->search))
                  ->orWhere('booking_reference', 'like', "%{$request->search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Availability search form (FR-4.1).
     */
    public function availability(Request $request): View
    {
        $roomTypes    = RoomType::orderBy('name')->get();
        $rooms        = collect();
        $searchParams = [];

        if ($request->filled(['check_in', 'check_out'])) {
            $from         = $request->check_in;
            $to           = $request->check_out;
            $roomTypeId   = $request->room_type_id ?: null;
            $rooms        = $this->availability->getAvailableRooms($from, $to, $roomTypeId);
            $searchParams = compact('from', 'to', 'roomTypeId');
        }

        return view('bookings.availability', compact('roomTypes', 'rooms', 'searchParams'));
    }

    public function create(Request $request): View
    {
        $guests    = Guest::orderBy('name')->get();
        $roomTypes = RoomType::orderBy('name')->get();
        $rooms     = Room::with('roomType')->where('status', '!=', 'maintenance')->orderBy('room_number')->get();

        // Pre-fill from availability search
        $selectedRoom = $request->room_id ? Room::with('roomType')->find($request->room_id) : null;

        return view('bookings.create', compact('guests', 'roomTypes', 'rooms', 'selectedRoom'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->createBooking->execute(array_merge(
                $request->validated(),
                ['created_by' => auth()->id()]
            ));

            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', "Booking {$booking->booking_reference} confirmed successfully.");
        } catch (RoomNotAvailableException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Booking $booking): View
    {
        $booking->load(['guest', 'room.roomType', 'checkInOut', 'invoice.payments', 'additionalServices', 'createdBy']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View
    {
        $guests = Guest::orderBy('name')->get();
        $rooms  = Room::with('roomType')->where('status', '!=', 'maintenance')->orderBy('room_number')->get();
        return view('bookings.edit', compact('booking', 'guests', 'rooms'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update($request->validated());
        return redirect()->route('bookings.show', $booking)->with('success', 'Booking updated.');
    }

    /**
     * Cancel a booking (FR-4.3).
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed bookings can be cancelled.');
        }

        // Free cancellation default — flag in PROGRESS.md
        // Business decision: free cancellation at any time before check-in.
        $booking->update(['status' => 'cancelled']);

        // If room was reserved for this booking, set back to available
        // (only if no other active booking holds this room)
        $room = $booking->room;
        $otherActiveBooking = Booking::where('room_id', $room->id)
            ->where('id', '!=', $booking->id)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->exists();

        if (!$otherActiveBooking && $room->status === 'reserved') {
            $room->update(['status' => 'available']);
        }

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled.');
    }
}
