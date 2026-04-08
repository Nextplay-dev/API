<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    Route::prefix('auth')->group(base_path('routes/api/auth.php'));
    Route::prefix('activities')
        ->group(base_path('routes/api/activity.php'));

    Route::prefix('tournaments')
        ->group(base_path('routes/api/tournament.php'));

    Route::prefix('activity-categories')
        ->group(base_path('routes/api/activity-category.php'));

});
