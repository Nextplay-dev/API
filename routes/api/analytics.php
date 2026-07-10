<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::controller(AnalyticsController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('analytics.index');
    });
