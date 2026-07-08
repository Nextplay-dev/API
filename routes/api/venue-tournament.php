<?php

use App\Http\Controllers\VenueTournamentController;
use Illuminate\Support\Facades\Route;

Route::controller(VenueTournamentController::class)
    ->scopeBindings()
    ->group(function () {
        Route::get('{venue}/tournaments/', 'index')
            ->name('venue.tournaments.index')
            ->middleware('permission:venue-tournament.view');
        Route::get('tournaments/{venueTournament}', 'show')
            ->name('venue.tournaments.show')
            ->middleware('permission:venue-tournament.view');
        Route::post('{venue}/tournaments/', 'store')
            ->name('venue.tournaments.store')
            ->middleware('permission:venue-tournament.create');
        Route::put('tournaments/{venueTournament}', 'update')
            ->name('venue.tournaments.update')
            ->middleware('permission:venue-tournament.update');
        Route::delete('tournaments/{venueTournament}', 'destroy')
            ->name('venue.tournaments.destroy')
            ->middleware('permission:venue-tournament.delete');
    });
