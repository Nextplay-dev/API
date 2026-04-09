<?php

use App\Http\Controllers\ActivityCategoryController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityCategoryController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('activity-category.index')
        ->middleware('permission:activity-category.view');
    Route::get('/{activityCategory}', 'show')
        ->name('activity-category.show')
        ->middleware('permission:activity-category.view');
    
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', 'store')
            ->name('activity-category.store')
            ->middleware('permission:activity-category.create');
        Route::put('/{activityCategory}', 'update')
            ->name('activity-category.update')
            ->middleware('permission:activity-category.update');
        Route::delete('/{activityCategory}', 'destroy')
            ->name('activity-category.destroy')
            ->middleware('permission:activity-category.delete');
    });
});
