<?php

namespace App\Actions\Booking;

use App\Models\BookingGuest;

class GetBookingGuestAction
{
    public function handle(string $token): ?BookingGuest
    {
        $guest = BookingGuest::with(['booking.resource.venue', 'booking.activity'])
            ->where(function ($query) use ($token) {
                $query->where('token', $token)
                    ->where('status', 'pending');
            })
            ->orWhere(function ($query) use ($token) {
                $query->where('token', $token)
                    ->whereHas('booking', function ($query) {
                        $query->where('end_at', '>=', now());
                    });
            })
            ->first();

        return $guest;
    }
}
