<?php

namespace App\Actions\Booking;

use App\Models\BookingGuest;
use App\Models\User;
use App\Notifications\FriendActivityNotification;

class JoinBookingAction
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
                'status' => 'accepted',
                'user_id' => $user ? $user->id : $guest->user_id,
            ]);

            $booking = $guest->booking;
            $booking->load('user');
            $friendName = $user ? $user->name : ($guest->user ? $guest->user->name : explode('@', $guest->email)[0]);

            $otherGuests = $booking->guests()
                ->where('status', 'accepted')
                ->where('id', '!=', $guest->id)
                ->whereNotNull('user_id')
                ->with('user')
                ->get()
                ->pluck('user');

            $usersToNotify = collect([$booking->user])
                ->concat($otherGuests)
                ->filter()
                ->unique('id');

            foreach ($usersToNotify as $userToNotify) {
                $userToNotify->notify(new FriendActivityNotification($booking, $friendName));
            }
        }

        return $guest;
    }
}
