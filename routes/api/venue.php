<?php

use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::controller(VenueController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('venue.index')
        ->middleware('permission:venue.view');
    Route::get('/{venue}', 'show')
        ->name('venue.show')
        ->middleware('permission:venue.view');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', 'store')
            ->name('venue.store')
            ->middleware('permission:venue.create');
        Route::put('/{venue}', 'update')
            ->name('venue.update')
            ->middleware('permission:venue.update');
        Route::delete('/{venue}', 'destroy')
            ->name('venue.destroy')
            ->middleware('permission:venue.delete');
    });
});
