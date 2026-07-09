<?php

use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TryEmailController;
use Illuminate\Support\Facades\Route;

Route::post('try-email', TryEmailController::class)
    ->name('try-email');

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

Route::post('apple/mobile', [AppleAuthController::class, 'mobile'])
    ->name('apple.mobile');

Route::post('logout', LogoutController::class)
    ->middleware('auth:sanctum')
    ->name('logout');
