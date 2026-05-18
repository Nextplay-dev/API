<?php

namespace App\Http\Controllers\Booking;

use App\Actions\Booking\GetBookingGuestAction;
use App\Actions\Booking\JoinBookingAction;
use App\Actions\Booking\DeclineBookingAction;
use App\Http\Controllers\Controller;
use App\Models\BookingGuest;
use Illuminate\Http\Request;

class BookingInvitationTokenController extends Controller
{
    public function show(BookingGuest $guest)
    {
        $guest->load('booking.resource.venue', 'booking.activity');

        return view('emails.response', [
            'success' => true,
            'status' => $guest->status,
            'guest' => $guest,
            'booking' => $guest->booking,
        ]);
    }

    public function join(Request $request, GetBookingGuestAction $getBookingGuestAction, JoinBookingAction $joinBookingAction)
    {
        $token = $request->query('token');
        if (!$token) {
            return view('emails.response', [
                'success' => false,
                'message' => 'No token provided.',
            ]);
        }

        $guest = $getBookingGuestAction->handle($token);
        if (!$guest) {
            return view('emails.response', [
                'success' => false,
                'message' => 'Invalid or expired invitation token.',
            ]);
        }
        $guest = $joinBookingAction->handle($guest);

        return redirect()->route('bookings.show', $guest);
    }

    public function decline(Request $request, GetBookingGuestAction $getBookingGuestAction, DeclineBookingAction $declineBookingAction)
    {
        $token = $request->query('token');
        if (!$token) {
            return view('emails.response', [
                'success' => false,
                'message' => 'No token provided.',
            ]);
        }

        $guest = $getBookingGuestAction->handle($token);
        if (!$guest) {
            return view('emails.response', [
                'success' => false,
                'message' => 'Invalid or expired invitation token.',
            ]);
        }
        $guest = $declineBookingAction->handle($guest);

        return redirect()->route('bookings.show', $guest);
    }
}
