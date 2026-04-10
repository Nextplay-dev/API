<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
