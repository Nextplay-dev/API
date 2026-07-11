<?php

use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::controller(WorkflowController::class)
    ->middleware('permission:back-office.administration.workflows')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/available', 'available');
        Route::get('/pending-reviews', 'pendingReviews');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::post('/{id}/signal', 'signal');
        Route::post('/{id}/abort', 'abort');
        Route::delete('/{id}', 'destroy');
    });
