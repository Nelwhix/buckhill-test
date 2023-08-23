<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/exchange', Nelwhix\CurrencyExchange\Controllers\CurrencyExchangeController::class);

    Route::prefix('admin')->group(function () {
        Route::post('/create', [RegisteredUserController::class, 'store']);
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);

        Route::middleware('auth.jwt')->group(function () {
            Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);

            Route::controller(UserController::class)->group(function () {
               Route::get('/user-listing', 'index');
               Route::put('/user-edit/{uuid}', 'update');
               Route::delete('/user-delete/{uuid}', 'destroy');
            });
        });
    });
});
