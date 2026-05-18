<?php

namespace App\Http\Controllers\Booking;

use App\Actions\Booking\JoinBookingAction;
use App\Actions\Booking\DeclineBookingAction;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingGuest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BookingInvitationController extends Controller
{
    public function join(Booking $booking, BookingGuest $bookingGuest, JoinBookingAction $joinBookingAction)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('booking.create') || $bookingGuest->user_id != $user->id) {
            return abort(403);
        }

        $bookingGuest = $joinBookingAction->handle($bookingGuest);
        
        return response()->json([], 201);
    }

    public function decline(Booking $booking, BookingGuest $bookingGuest, DeclineBookingAction $declineBookingAction)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('booking.create') || $bookingGuest->user_id != $user->id) {
            return abort(403);
        }

        $bookingGuest = $declineBookingAction->handle($bookingGuest);
        
        return response()->json([], 201);
    }
}
