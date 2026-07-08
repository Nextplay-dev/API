<?php

namespace App\Actions\Booking;

use App\DTOs\InviteGuestDTO;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\User;
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
                    'email' => 'The booking has reached the maximum capacity of the resource ('.$booking->resource->capacity.' players).',
                ]);
            }
        }

        if ($dto->email) {
            if (BookingGuest::where('email', $dto->email)->where('booking_id', $booking->id)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This user has already been invited.',
                ]);
            }
        }

        $guestUser = $dto->email ? User::where('email', $dto->email)->first() : null;

        $token = Str::random(40);

        $guest = BookingGuest::create([
            'user_id' => $guestUser?->id ?? null,
            'booking_id' => $booking->id,
            'email' => $dto->email,
            'token' => $token,
            'status' => $dto->email ? 'pending' : 'accepted',
        ]);

        if ($dto->email) {
            $notification = new InviteGuestNotification($booking, $guest);

            if ($guestUser) {
                $guestUser->notify($notification);
                $guest->update([
                    'notification_id' => $notification->id,
                ]);
            } else {
                Notification::route('mail', $dto->email)
                    ->notify($notification);
            }

        }

        return $guest;
    }
}
