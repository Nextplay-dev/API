<?php

use App\Http\Controllers\My\MyActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [MyActivityController::class, 'index'])
        ->middleware('permission:my-activity.view')
        ->name('my-activities.index');
    Route::get('/{activity}', [MyActivityController::class, 'show'])
        ->middleware('permission:my-activity.view')
        ->name('my-activities.show');
    Route::put('/{activity}', [MyActivityController::class, 'update'])
        ->middleware('permission:my-activity.update')
        ->name('my-activities.update');
});
