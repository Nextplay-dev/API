<?php

use App\Http\Controllers\Tournament\TournamentBookController;
use App\Http\Controllers\Tournament\TournamentCancelBookingController;
use App\Http\Controllers\Tournament\TournamentController;
use Illuminate\Support\Facades\Route;

Route::controller(TournamentController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('tournament.index');
    Route::get('/{tournament}', 'show')
        ->name('tournament.show');
});

Route::post('{tournament}/book', TournamentBookController::class)
    ->middleware('auth:sanctum')
    ->name('tournament.book');

    Route::post('{tournament}/cancel-booking', TournamentCancelBookingController::class)
    ->middleware('auth:sanctum')
    ->name('tournament.cancel-booking');
