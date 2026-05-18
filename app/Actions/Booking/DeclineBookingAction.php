<?php

namespace App\Actions\Booking;

use App\Events\NotificationDeleted;
use App\Models\BookingGuest;
use App\Models\User;

class DeclineBookingAction
{
    public function handle(BookingGuest $guest): ?BookingGuest
    {
        $user = User::where('email', $guest->email)->first();

        if ($guest->status == 'pending') {
            $guest->update([
                'status' => 'rejected',
                'user_id' => $user ? $user->id : $guest->user_id,
            ]);

            if ($guest->user_id && $guest->notification_id) {
                NotificationDeleted::dispatch($guest->user_id, $guest->notification_id);
            }
            $guest->notification()->delete();

            $booking = $guest->booking;
            $booking->load('user');
            $friendName = $user ? $user->name : ($guest->user ? $guest->user->name : explode('@', $guest->email)[0]);

            if ($booking->user) {
                $booking->user->notify(new \App\Notifications\InvitationDeclinedNotification($booking, $friendName));
            }
        }

        return $guest;
    }
}
