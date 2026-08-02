<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\GuestReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestReviewController extends Controller
{
    public function index(): View
    {
        $reviews = GuestReview::with(['guest', 'booking.room.roomType'])->latest()->paginate(15);
        return view('reviews.index', compact('reviews'));
    }

    public function storePublic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_reference' => ['required', 'string'],
            'rating'            => ['required', 'integer', 'min:1', 'max:5'],
            'headline'          => ['required', 'string', 'max:255'],
            'comment'           => ['required', 'string', 'max:1000'],
        ]);

        $booking = Booking::where('booking_reference', trim($validated['booking_reference']))->first();

        if (!$booking) {
            return back()->with('error', 'Booking reference not found. Please check your reservation receipt.');
        }

        $review = GuestReview::create([
            'booking_id'   => $booking->id,
            'guest_id'     => $booking->guest_id,
            'rating'       => $validated['rating'],
            'headline'     => $validated['headline'],
            'comment'      => $validated['comment'],
            'is_published' => true,
        ]);

        AuditLog::log('booking', 'review.created', "Guest {$booking->guest->name} submitted a {$review->rating}-star review.");

        return redirect()->to('/#reviews')->with('success', 'Thank you for your review!');
    }

    public function togglePublish(GuestReview $review): RedirectResponse
    {
        $review->update(['is_published' => !$review->is_published]);
        return back()->with('success', 'Review publication status updated.');
    }
}
