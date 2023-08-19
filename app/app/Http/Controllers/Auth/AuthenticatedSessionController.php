<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Request;
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
            new OA\Response(response: Response::HTTP_OK, description: "login success"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Unprocessable entity"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Bad Request"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function store(LoginRequest $request): \Illuminate\Http\Response
    {
        $formFields = $request->validated();

        $user = User::where('email', $formFields['email'])->firstOrFail();

        if (!Hash::check($formFields['password'], $user->password)) {
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

    #[OA\Get(
        path: "/api/v1/admin/logout",
        summary: "Logout an Admin Account",
        security: [
            [
                'bearerAuth' => []
            ]
        ],
        tags: ["Admin"],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: "logout success"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized"),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: "Server Error")
        ]
    )]
    public function destroy(Request $request): \Illuminate\Http\Response
    {
        $tokenMetadata = $this->tokenService->getTokenMetadata($request->bearerToken(), true);
        $tokenModel = JwtToken::whereUniqueId($tokenMetadata->claims()->get('token_uuid'))->first();
        $tokenModel->delete();

        return response([
            'status' => 'success',
            'message' => 'tokens revoked successfully'
        ]);
    }
}
