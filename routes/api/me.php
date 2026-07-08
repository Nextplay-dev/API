<?php

use App\Http\Controllers\Auth\GetMeStatsController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\MeNotificationController;
use App\Http\Controllers\Auth\MePreferredCategoriesController;
use App\Http\Controllers\Auth\StoreMePushTokenController;
use App\Http\Controllers\Auth\UploadMePictureController;
use App\Http\Controllers\Booking\MeBookingController;
use Illuminate\Support\Facades\Route;

Route::controller(MeController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->middleware('permission:me.view')
            ->name('me.index');

        Route::put('/', 'update')
            ->middleware('permission:me.update')
            ->name('me.update');

        Route::delete('/', 'destroy')
            ->middleware('permission:me.delete')
            ->name('me.destroy');

        Route::post('picture', UploadMePictureController::class)
            ->middleware('permission:me.update')
            ->name('me.picture.upload');

        Route::post('push-token', StoreMePushTokenController::class)
            ->middleware('permission:me.update')
            ->name('me.push-token.store');

        Route::controller(MeBookingController::class)->group(function () {

            Route::get('bookings', 'index')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.index');

            Route::get('joined-bookings', 'joined')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.joined');

            Route::get('joined-bookings/{id}', 'showJoined')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.joined.show');

            Route::get('bookings/{id}', 'show')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.show');

            Route::post('bookings/{id}/invite', 'invite')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.invite');

            Route::post('bookings/{id}/scores', 'score')
                ->middleware('permission:me.booking.view')
                ->name('me.bookings.score');
        });

        Route::get('preffered-categories', [MePreferredCategoriesController::class, 'index'])
            ->middleware('permission:me.view')
            ->name('me.preffered-categories.index');

        Route::controller(MeNotificationController::class)->group(function () {

            Route::get('notifications', 'index')
                ->middleware('permission:me.view')
                ->name('me.notifications.index');

            Route::post('notifications/{notification}/read', 'read')
                ->middleware('permission:me.view')
                ->name('me.notifications.read');

            Route::delete('notifications/{notification}', 'destroy')
                ->middleware('permission:me.view')
                ->name('me.notifications.destroy');

        });

        Route::get('stats', GetMeStatsController::class)
            ->middleware('permission:me.view')
            ->name('me.stats.index');

    });
