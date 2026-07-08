<?php

use App\Http\Controllers\ListActivityAvailableSlotController;
use App\Http\Controllers\ListActivityResourceAvailableSlotController;
use App\Http\Controllers\VenueActivityController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::controller(VenueController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('venue.index')
            ->middleware('permission:venue.view');

        Route::prefix('/{venue}')
            ->scopeBindings()
            ->group(function () {
                Route::get('/', 'show')
                    ->name('venue.show')
                    ->middleware('permission:venue.view');

                Route::get('/activities/{activity}/available-slots', ListActivityAvailableSlotController::class)
                    ->name('venue.activities.available-slots')
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

Route::controller(VenueActivityController::class)
    ->scopeBindings()
    ->group(function () {
        Route::get('{venue}/activities/', 'index')
            ->name('venue.activities.index')
            ->middleware('permission:venue.view');
        Route::get('{venue}/activities/{activity}', 'show')
            ->name('venue.activities.show')
            ->middleware('permission:venue.view');
        Route::post('{venue}/activities/', 'store')
            ->name('venue.activities.store')
            ->middleware('permission:venue.create');
        Route::put('{venue}/activities/{activity}', 'update')
            ->name('venue.activities.update')
            ->middleware('permission:venue.update');
        Route::delete('{venue}/activities/{activity}', 'destroy')
            ->name('venue.activities.destroy')
            ->middleware('permission:venue.delete');
    });
