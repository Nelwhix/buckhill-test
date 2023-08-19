<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class AuthenticatedSessionController extends Controller
{
    public function __construct(protected TokenService $tokenService){}

    #[OA\Post(
        path: "/api/v1/admin/login",
        summary: "Login an Admin Account",
        requestBody: new OA\RequestBody(required: true,
            content: new OA\MediaType(mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(required: ["email", "password"],
                    properties: [
                        new OA\Property(property: 'email', description: "User email", type: "string"),
                        new OA\Property(property: 'password', description: "User password", type: "string"),
                    ]
                ))),
        tags: ["Admin"],
        responses: [
            new OA\Response(response: 200, description: "login success"),
            new OA\Response(response: 422, description: "Unprocessable entity"),
            new OA\Response(response: 400, description: "Bad Request"),
            new OA\Response(response: 500, description: "Server Error")
        ]
    )]
    public function store(LoginRequest $request): \Illuminate\Http\Response
    {
        $formFields = $request->validated();

        $user = User::where('email', $formFields['email'])->firstOrFail();

        if (!Hash::check(trim($formFields['password']), $user->password)) {
            return response([
                'status' => 'failed',
                'message' => 'invalid credentials'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($user->is_admin === 0) {
            return response([
                'status' => 'failed',
                'message' => 'invalid credentials'
            ], Response::HTTP_BAD_REQUEST);
        }

        $accessTokenModel = JwtToken::createToken($user->id, true);
        $refreshTokenModel = JwtToken::createToken($user->id, false);
        $tokenVO = $this->tokenService->createToken($user, $accessTokenModel, $refreshTokenModel);

        return response([
            'status' => 'success',
            'message' => 'login success',
            'data' => [
                'access_token' => $tokenVO->accessToken->toString(),
                'refresh_token' => $tokenVO->accessToken->toString(),
                'user' => $user
            ]
        ]);
    }
}
