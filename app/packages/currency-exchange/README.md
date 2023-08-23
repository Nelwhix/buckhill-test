# Nelwhix/currency-exchange
A Laravel package to convert an amount from Euro to other
currencies

## Installation locally
Edit composer.json to look like this
```json 
  {
  "repositories": [
    {
      "type": "path",
      "url": "./packages/currency-exchange"
    }
  ],
   "require": {
       "nelwhix/currency-exchange": "dev-main"
   }
}
```
then run:
```bash 
    composer update 
```

## Publish config
publish the config file with:
```bash 
    php artisan vendor:publish --provider="Nelwhix\CurrencyExchange\CurrencyExchangeServiceProvider" --tag="config"
```

## Usage
Default Route (GET) [/exchange?amount=100&to=USD]

swagger docs is https://localhost:8088/api/v1/docs

### Testing
```bash 
    ./vendor/bin/pest
```
