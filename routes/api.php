<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::prefix('auth')
        ->group(base_path('routes/api/auth.php'));
    Route::prefix('me')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/me.php'));
    Route::prefix('venues')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/venue.php'));
    Route::prefix('venues')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/venue-tournament.php'));
    Route::prefix('my-venues')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/my-venue.php'));
    Route::prefix('tournaments')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/tournament.php'));
    Route::prefix('categories')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/category.php'));
    Route::prefix('locations')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/location.php'));
    Route::prefix('users')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/user.php'));
    Route::prefix('roles')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/role.php'));
    Route::prefix('permissions')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/permission.php'));
    Route::prefix('bookings')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/booking.php'));
    Route::prefix('bug-reports')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/bug-report.php'));
    Route::prefix('analytics')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/analytics.php'));
    Route::apiResource('venues.resources', ResourceController::class)
        ->middleware('auth:sanctum')
        ->shallow();
    Route::get('resources/{resource}/bookings', [BookingController::class, 'indexByResource'])
        ->name('resource.bookings.index')
        ->middleware('auth:sanctum');
    Route::apiResource('resources.availabilities', AvailabilityController::class)
        ->shallow()
        ->middleware('auth:sanctum');
    Route::apiResource('resources.exceptions', ExceptionController::class)
        ->shallow()
        ->middleware('auth:sanctum');
});
