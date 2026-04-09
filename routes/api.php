<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::prefix('auth')->group(base_path('routes/api/auth.php'));
    Route::prefix('activities')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/activity.php'));

    Route::prefix('tournaments')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/tournament.php'));

    Route::prefix('activity-categories')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/activity-category.php'));

    Route::prefix('users')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/user.php'));

    Route::prefix('roles')
        ->middleware('auth:sanctum')
        ->group(base_path('routes/api/role.php'));

});
