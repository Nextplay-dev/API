<?php

use App\Http\Controllers\BookingController;

Route::controller(BookingController::class)->group(function () {

    Route::post('', 'store')
        ->name('booking.store')
        ->middleware('permission:booking.create');

});
