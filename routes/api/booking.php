<?php

use App\Http\Controllers\Booking\BookingInvitationController;
use App\Http\Controllers\BookingController;

Route::controller(BookingController::class)->group(function () {

    Route::post('', 'store')
        ->name('booking.store')
        ->middleware('permission:booking.create');

    Route::prefix('{booking}')->group(function () {
        Route::controller(BookingInvitationController::class)->group(function () {
            Route::post('join/{bookingGuest}', 'join')
                ->middleware('permission:booking.create')
                ->name('booking.join');
            Route::post('decline/{bookingGuest}', 'decline')
                ->middleware('permission:booking.create')
                ->name('booking.decline');
        });
    });

});
