<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::controller(ActivityController::class)
->group(function () {
    Route::get('/', 'index')
        ->name('activity.index');
    Route::get('/{activity}', 'show')
        ->name('activity.show');
});
