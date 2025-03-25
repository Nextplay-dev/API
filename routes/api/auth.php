<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('login', LoginController::class)
->name('login');

Route::post('register', RegisterController::class)
->name('register');

Route::post('logout', LogoutController::class)
->middleware('auth:sanctum')
->name('logout');

Route::controller(MeController::class)
->middleware('auth:sanctum')
->prefix('me')
->group(function () {
    Route::get('/', 'index')
    ->name('me.index');
});
