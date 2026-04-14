<?php

use App\Http\Controllers\My\ListMyVenueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', ListMyVenueController::class)
        ->middleware('permission:my-venue.view')
        ->name('my-activities.index');
});
