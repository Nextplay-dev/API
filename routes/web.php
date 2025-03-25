<?php

use App\Http\Controllers\Core\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
