<?php

namespace App\Actions\Booking;

use App\Models\BookingGuest;
use App\Models\User;

class DeclineBookingAction
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

        if (!$guest) {
            return null;
        }

        $user = User::where('email', $guest->email)->first();

        if ($guest->status == 'pending') {
            $guest->update([
                'status' => 'rejected',
                'user_id' => $user ? $user->id : $guest->user_id,
            ]);
        }

        return $guest;
    }
}
