<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[
    OA\Info(version: "1.0.0", description: "petshop api", title: "Petshop-api Documentation"),
    OA\Server(url: 'http://localhost:8088', description: "Petshop-api server"),
    OA\SecurityScheme( securityScheme: 'bearerAuth', type: "http", name: "Authorization", in: "header", scheme: "bearer")
]
class Controller extends BaseController
{

    use AuthorizesRequests, ValidatesRequests;
}
