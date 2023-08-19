<?php

use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('/create', [RegisteredUserController::class, 'store']);
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);

        Route::middleware('auth.jwt')->group(function () {
            Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);
        });
    });
});
