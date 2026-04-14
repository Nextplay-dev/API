<?php

use App\Http\Controllers\Booking\MeBookingController;
use App\Http\Controllers\Auth\MeController;

Route::controller(MeController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->middleware('permission:me.view')
            ->name('me.index');

        Route::controller(MeBookingController::class)->group(function() {

            Route::get('bookings', 'index')
                ->middleware('permission:me.view')
                ->name('me.bookings.index');

        });

    });