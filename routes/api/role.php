<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RoleController::class, 'index'])
    ->middleware('permission:role.view')
    ->name('role.index');
Route::get('/{role}', [RoleController::class, 'show'])
    ->middleware('permission:role.view')
    ->name('role.show');
Route::post('/', [RoleController::class, 'store'])
    ->middleware('permission:role.create')
    ->name('role.store');
Route::put('/{role}', [RoleController::class, 'update'])
    ->middleware('permission:role.update')
    ->name('role.update');
Route::delete('/{role}', [RoleController::class, 'destroy'])
    ->middleware('permission:role.delete')
    ->name('role.destroy');
