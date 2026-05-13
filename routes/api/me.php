<?php

use App\Http\Controllers\Booking\MeBookingController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\MePreferredCategoriesController;
use App\Http\Controllers\Auth\MeNotificationController;
use Illuminate\Support\Facades\Route;

Route::controller(MeController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->middleware('permission:me.view')
            ->name('me.index');

        Route::controller(MeBookingController::class)->group(function() {

            Route::get('bookings', 'index')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.index');

        });

        Route::get('preffered-categories', [MePreferredCategoriesController::class, 'index'])
            ->middleware('permission:me.view')
            ->name('me.preffered-categories.index');

        Route::controller(MeNotificationController::class)->group(function() {

            Route::get('notifications', 'index')
                ->middleware('permission:me.view')
                ->name('me.notifications.index');

            Route::post('notifications/{notification}/read', 'read')
                ->middleware('permission:me.view')
                ->name('me.notifications.read');

        });

    });