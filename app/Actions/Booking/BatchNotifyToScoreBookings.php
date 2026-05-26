<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Carbon\Carbon;

class BatchNotifyToScoreBookings
{
    public function __construct(
        protected NotifyToScoreBooking $notifyToScoreBooking
    ) {}

    public function handle(): void
    {
        $bookings = Booking::where('status', 'confirmed')
            ->where('end_at', '<', Carbon::now())
            ->whereNull('scoring_notification_id')
            ->get();

        foreach ($bookings as $booking) {
            $this->notifyToScoreBooking->handle($booking);
        }
    }
}
