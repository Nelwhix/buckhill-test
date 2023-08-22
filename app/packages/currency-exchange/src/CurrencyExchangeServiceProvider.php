<?php declare(strict_types=1);

namespace Nelwhix\CurrencyExchange;

use Illuminate\Support\ServiceProvider;

class CurrencyExchangeServiceProvider extends ServiceProvider
{
   public function boot(): void {
       $this->loadRoutesFrom(__DIR__. '/../routes/api.php');
       $this->publishes([
           __DIR__.'/../config/currency-exchange.php' => config_path('currency-exchange.php'),
       ]);
   }
}
