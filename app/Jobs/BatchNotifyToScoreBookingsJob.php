<?php

namespace App\Jobs;

use App\Actions\Booking\BatchNotifyToScoreBookings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BatchNotifyToScoreBookingsJob implements ShouldQueue
{
    use Queueable;

    public function handle(BatchNotifyToScoreBookings $action): void
    {
        $action->handle();
    }
}
