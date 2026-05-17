<?php

namespace App\Actions\Booking;

use App\DTOs\InviteGuestDTO;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Notifications\InviteGuestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InviteGuestAction
{
    public function handle(int $bookingId, InviteGuestDTO $dto): BookingGuest
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['resource', 'guests'])
            ->findOrFail($bookingId);

        if ($booking->resource) {
            $currentCount = 1 + $booking->guests()->where('status', '!=', 'rejected')->count();
            if ($currentCount >= $booking->resource->capacity) {
                throw ValidationException::withMessages([
                    'email' => 'The booking has reached the maximum capacity of the resource (' . $booking->resource->capacity . ' players).',
                ]);
            }
        }

        $token = Str::random(40);

        $guest = BookingGuest::create([
            'booking_id' => $booking->id,
            'email' => $dto->email,
            'token' => $token,
            'status' => 'pending',
        ]);

        Notification::route('mail', $dto->email)
            ->notify(new InviteGuestNotification($booking, $token));

        return $guest;
    }
}
