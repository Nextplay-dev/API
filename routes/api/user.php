<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('user.index')
            ->middleware('permission:user.view');
        Route::get('/{user}', 'show')
            ->name('user.show')
            ->middleware('permission:user.view');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', 'store')
                ->name('user.store')
                ->middleware('permission:user.create');
            Route::put('/{user}', 'update')
                ->name('user.update')
                ->middleware('permission:user.update');
            Route::delete('/{user}', 'destroy')
                ->name('user.destroy')
                ->middleware('permission:user.delete');
        });
    });
