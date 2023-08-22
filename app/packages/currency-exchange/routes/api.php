<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

const CURRENCY_SOURCE = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";

Route::get('/exchange', function (Request $request) {
   $amount = $request->query('amount');
   $targetCurrency = $request->query('currency');

   if ($amount === null || $targetCurrency === null) {
       return response([
           'status' => 'failed',
           'message' => 'amount and target currency is required'
       ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
   }
   $amount = (float) $amount;

   $currencySource = file_get_contents(CURRENCY_SOURCE);
   if (!$currencySource) {
       return response([
           'status' => 'failed',
           'message' => 'could not get currency list'
       ], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
   }
    $xml = simplexml_load_string($currencySource);
    if (!$xml) {
        return response([
            'status' => 'failed',
            'message' => 'could not parse'
        ], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $cube_element = $xml->Cube->Cube->Cube;
    $currency_rates = [];

    foreach ($cube_element as $currency_element) {
        $currency_rates[] = [
            "currency" => (string) $currency_element['currency'],
            "rate" => (float) $currency_element['rate']
        ];
    }

   if (!validateCurrency($targetCurrency, $currency_rates)) {
       return response([
           'status' => 'failed',
           'message' => "cannot convert from euro to $targetCurrency, check".CURRENCY_SOURCE."for supported conversions"
       ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
   }

   return response([
       'status' => 'success',
       'message' => 'conversion success',
       'data' => [
            $targetCurrency => convertCurrency($amount, getRate($targetCurrency, $currency_rates))
       ]
   ]);
});

function validateCurrency(string $targetCurrency, array $currency_rates): bool {
    $currency_exists = false;

    foreach ($currency_rates as $currency_data) {
        if ($currency_data["currency"] === $targetCurrency) {
            $currency_exists = true;
            break;
        }
    }

    return $currency_exists;
}

function getRate(string $targetCurrency, array $currency_rates): float|null {
    foreach ($currency_rates as $currency_data) {
        if ($currency_data["currency"] === $targetCurrency) {
            return (float) $currency_data["rate"];
        }
    }

    return null;
}

function convertCurrency(float $amount, float $rate): float {
    return $amount * $rate;
}
