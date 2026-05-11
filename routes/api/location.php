<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::controller(LocationController::class)
    ->group(function () {
        Route::get('/cities/autocomplete', 'autocomplete')
            ->name('location.cities.autocomplete')
            ->middleware('permission:venue.view');

        Route::get('/places/{placeId}', 'show')
            ->name('location.places.show')
            ->middleware('permission:venue.view');
    });