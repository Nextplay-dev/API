<?php

namespace App\Http\Controllers\Booking;

use App\Actions\Booking\JoinBookingAction;
use App\Actions\Booking\DeclineBookingAction;
use App\Http\Controllers\Controller;
use App\Models\BookingGuest;
use Illuminate\Http\Request;

class BookingInvitationController extends Controller
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

    public function join(Request $request, JoinBookingAction $action)
    {
        $token = $request->query('token');
        if (!$token) {
            return view('emails.response', [
                'success' => false,
                'message' => 'No token provided.',
            ]);
        }

        $guest = $action->handle($token);
        if (!$guest) {
            return view('emails.response', [
                'success' => false,
                'message' => 'Invalid or expired invitation token.',
            ]);
        }

        return redirect()->route('bookings.show', $guest);
    }

    public function decline(Request $request, DeclineBookingAction $action)
    {
        $token = $request->query('token');
        if (!$token) {
            return view('emails.response', [
                'success' => false,
                'message' => 'No token provided.',
            ]);
        }

        $guest = $action->handle($token);
        if (!$guest) {
            return view('emails.response', [
                'success' => false,
                'message' => 'Invalid or expired invitation token.',
            ]);
        }

        return redirect()->route('bookings.show', $guest);
    }
}
