<?php

use App\Http\Controllers\My\MyVenueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [MyVenueController::class, 'index'])
        ->middleware('permission:my-venue.view')
        ->name('my-activities.index');
    Route::get('/{venue}', [MyVenueController::class, 'show'])
        ->middleware('permission:my-venue.view')
        ->name('my-activities.show');
    Route::put('/{venue}', [MyVenueController::class, 'update'])
        ->middleware('permission:my-venue.update')
        ->name('my-activities.update');
});
