<?php

use App\Http\Controllers\Booking\BookingInvitationTokenController;
use App\Http\Controllers\Core\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::get('/api-docs', function () {
    return view('swagger');
});

Route::get('/docs/openapi.json', function () {
    return response()->file(storage_path('doc/openapi.json'));
});

Route::get('/bookings/join', [BookingInvitationTokenController::class, 'join'])->name('bookings.join');
Route::get('/bookings/decline', [BookingInvitationTokenController::class, 'decline'])->name('bookings.decline');
Route::get('/bookings/{guest}', [BookingInvitationTokenController::class, 'show'])->name('bookings.show');
