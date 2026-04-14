<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\SlotController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::prefix('auth')->group(base_path('routes/api/auth.php'));
    Route::prefix('venues')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/venue.php'));

    Route::prefix('my-venues')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/my-venue.php'));

    Route::prefix('tournaments')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/tournament.php'));

    Route::prefix('categories')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/category.php'));

    Route::prefix('users')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/user.php'));

    Route::prefix('roles')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/role.php'));

    Route::prefix('permissions')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/permission.php'));

    Route::post('bookings', [BookingController::class, 'store'])
        ->middleware('auth:sanctum');

    Route::get('slots', [SlotController::class, 'index'])
        ->middleware('auth:sanctum');

});
