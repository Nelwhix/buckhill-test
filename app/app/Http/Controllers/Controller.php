<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[
    OA\Info(version: "1.0.0", description: "petshop api", title: "Petshop-api Documentation"),
    OA\Server(url: 'http://localhost:8088', description: "Petshop-api server"),
    OA\SecurityScheme( securityScheme: 'bearerAuth', type: "http", name: "Authorization", in: "header", scheme: "bearer"),

    OA\Get(
    path: "/api/v1/exchange",
    summary: "Convert from Euro to any supported currency. supported currencies: https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml",
    tags: ["Currency Exchange"],
    parameters: [
        new OA\Parameter(name: "amount", description: "amount to convert", in: "path", required: true),
        new OA\Parameter(name: "to", description: "currency to convert to", in: "path", required: true)
    ],
    responses: [
        new OA\Response(response: Response::HTTP_OK, description: "conversion"),
        new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
        new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Not found"),
        new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error"),
    ]
)
]
class Controller extends BaseController
{

    use AuthorizesRequests, ValidatesRequests;
}
