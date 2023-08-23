<?php declare(strict_types=1);

namespace Nelwhix\CurrencyExchange\Controllers;

use Illuminate\Http\Request;

class CurrencyExchangeController extends \Illuminate\Routing\Controller
{
    const CURRENCY_SOURCE = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";

    public function __invoke(Request $request): \Illuminate\Http\Response
    {
        $amount = $request->query('amount');
        $targetCurrency = $request->query('to');

        if ($amount === null || $targetCurrency === null) {
            return response([
                'status' => 'failed',
                'message' => 'amount and target currency is required'
            ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }
        $amount = (float)$amount;

        $currency_rates = $this->getCurrencyList();
        if ($currency_rates === null) {
            return response([
                'status' => 'failed',
                'message' => 'could not get currency list'
            ], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$this->validateCurrency($targetCurrency, $currency_rates)) {
            return response([
                'status' => 'failed',
                'message' => "cannot convert from euro to $targetCurrency, check " . self::CURRENCY_SOURCE . " for supported conversions"
            ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

        return response([
            'status' => 'success',
            'message' => 'conversion success',
            'data' => [
                $targetCurrency => $this->convertCurrency($amount, $this->getRate($targetCurrency, $currency_rates))
            ]
        ]);
    }

    private function getCurrencyList(): array|null {
        $currencySource = file_get_contents(self::CURRENCY_SOURCE);
        if (!$currencySource) {
            return null;
        }

        $xml = simplexml_load_string($currencySource);
        if (!$xml) {
          return null;
        }

        $cube_element = $xml->Cube->Cube->Cube;
        $currency_rates = [];

        foreach ($cube_element as $currency_element) {
            $currency_rates[] = [
                "currency" => (string)$currency_element['currency'],
                "rate" => (float)$currency_element['rate']
            ];
        }

        return $currency_rates;
    }

    private function validateCurrency(string $targetCurrency, array $currency_rates): bool
    {
        $currency_exists = false;

        foreach ($currency_rates as $currency_data) {
            if ($currency_data["currency"] === $targetCurrency) {
                $currency_exists = true;
                break;
            }
        }

        return $currency_exists;
    }

    private function getRate(string $targetCurrency, array $currency_rates): float|null
    {
        foreach ($currency_rates as $currency_data) {
            if ($currency_data["currency"] === $targetCurrency) {
                return (float)$currency_data["rate"];
            }
        }

        return null;
    }

    private function convertCurrency(float $amount, float $rate): float
    {
        return $amount * $rate;
    }

}
