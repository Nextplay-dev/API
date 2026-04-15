<?php

use App\Http\Controllers\Core\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::get('/api-docs', function () {
    return view('swagger');
});

Route::get('/docs/openapi.json', function () {
    return response()->file(storage_path('doc/openapi.json'));
});