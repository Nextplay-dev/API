<?php

use App\Http\Controllers\ListActivityResourceAvailableSlotController;
use App\Http\Controllers\VenueController;
use App\Models\Activity;
use App\Models\Resource;
use Illuminate\Support\Facades\Route;

Route::model('activity', Activity::class);
Route::model('resource', Resource::class);

Route::controller(VenueController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('venue.index')
        ->middleware('permission:venue.view');

    Route::prefix('/{venue}')->group(function () {
        Route::get('/', 'show')
            ->name('venue.show')
            ->middleware('permission:venue.view');

        Route::get('/activities/{activity}/resources/{resource}/available-slots', ListActivityResourceAvailableSlotController::class)
            ->name('venue.activities.resources.available-slots')
            ->middleware('permission:venue.view');
    });


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
