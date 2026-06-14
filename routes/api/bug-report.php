<?php

use App\Http\Controllers\BugReportController;
use Illuminate\Support\Facades\Route;

Route::controller(BugReportController::class)
    ->group(function () {
        Route::post('/', 'store')
            ->name('bug-report.store')
            ->middleware('permission:bug-report.create');

        Route::get('/', 'index')
            ->name('bug-report.index')
            ->middleware('permission:bug-report.view');

        Route::delete('/{bugReport}', 'destroy')
            ->name('bug-report.destroy')
            ->middleware('permission:bug-report.delete');
    });
