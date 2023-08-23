<?php

use Illuminate\Support\Facades\Route;
use Nelwhix\CurrencyExchange\Controllers\CurrencyExchangeController;

Route::get('/exchange', CurrencyExchangeController::class);

