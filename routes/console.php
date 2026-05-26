<?php

use App\Jobs\BatchNotifyToScoreBookingsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:batch-scoring', function () {
    BatchNotifyToScoreBookingsJob::dispatch();
})->purpose('Dispatch the batch scoring notification job');

Schedule::job(new BatchNotifyToScoreBookingsJob)->everyMinute();
