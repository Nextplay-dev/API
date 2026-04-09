<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('activity.index');
    Route::get('/{activity}', 'show')
        ->name('activity.show');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', 'store')
            ->name('activity.store')
            ->middleware('permission:activity.create');
        Route::put('/{activity}', 'update')
            ->name('activity.update')
            ->middleware('permission:activity.update');
        Route::delete('/{activity}', 'destroy')
            ->name('activity.destroy')
            ->middleware('permission:activity.delete');
    });
});
