<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::controller(CategoryController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('category.index')
        ->middleware('permission:category.view');
    Route::get('/{category}', 'show')
        ->name('category.show')
        ->middleware('permission:category.view');
    
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', 'store')
            ->name('category.store')
            ->middleware('permission:category.create');
        Route::put('/{category}', 'update')
            ->name('category.update')
            ->middleware('permission:category.update');
        Route::delete('/{category}', 'destroy')
            ->name('category.destroy')
            ->middleware('permission:category.delete');
    });
});
