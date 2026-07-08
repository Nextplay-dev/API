<?php

use App\Http\Controllers\Tournament\TournamentBookController;
use App\Http\Controllers\Tournament\TournamentCancelBookingController;
use App\Http\Controllers\Tournament\TournamentController;
use Illuminate\Support\Facades\Route;

Route::controller(TournamentController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('tournament.index')
            ->middleware('permission:tournament.view');
        Route::get('/{tournament}', 'show')
            ->name('tournament.show')
            ->middleware('permission:tournament.view');
    });

Route::post('{tournament}/book', TournamentBookController::class)
    ->middleware('auth:sanctum')
    ->middleware('permission:tournament.book')
    ->name('tournament.book');

Route::post('{tournament}/cancel-booking', TournamentCancelBookingController::class)
    ->middleware('auth:sanctum')
    ->middleware('permission:tournament.cancel-booking')
    ->name('tournament.cancel-booking');
