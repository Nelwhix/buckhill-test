<?php

use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('/admin/create', [RegisteredUserController::class, 'store']);
    });

});
