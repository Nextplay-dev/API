<?php

use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('login', LoginController::class)
    ->name('login');

Route::post('register', RegisterController::class)
    ->name('register');

Route::get('google/redirect', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

Route::get('apple/redirect', [AppleAuthController::class, 'redirect'])
    ->name('apple.redirect');

Route::match(['get', 'post'], 'apple/callback', [AppleAuthController::class, 'callback'])
    ->name('apple.callback');

Route::post('logout', LogoutController::class)
    ->middleware('auth:sanctum')
    ->name('logout');
