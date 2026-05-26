<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use App\Notifications\ScoreYourBookingNotification;

class NotifyToScoreBooking
{
    public function handle(Booking $booking): void
    {
        $booking->loadMissing('user');

        $notification = new ScoreYourBookingNotification($booking);
        $booking->user->notify($notification);

        $booking->update([
            'scoring_notification_id' => $notification->id,
        ]);
    }
}
