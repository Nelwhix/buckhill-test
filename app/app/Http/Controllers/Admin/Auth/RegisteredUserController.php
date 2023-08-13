<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/admin/create",
     *     summary="Create an Admin account",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="last_name",
     *                      type="string"
     *                  ),
     *                 @OA\Property(
     *                      property="email",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="password",
     *                      type="string"
     *                  ),
     *                  @OA\Property  (
     *                      property="password_confirmation",
     *                      type="string"
     *                  ),
     *                   @OA\Property  (
     *                      property="address",
     *                      type="string"
     *                  ),
     *                  @OA\Property  (
     *                      property="phone_number",
     *                      type="string"
     *                  ),
     *                 example={"first_name": "Bruno", "last_name": "Isioma", "email": "brunoisioma1@gmail.com", "password": "test123", "password_confirmation": "test123", "address": "Ba sing se", "phone_number": "011111"}
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),

     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     * )
     */
    public function store(RegisterRequest $request): Response
    {
        $formFields = $request->validated();

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'first_name' => $formFields['first_name'],
            'last_name' => $formFields['last_name'],
            'is_admin' => 1,
            'email' => $formFields['email'],
            'password' => Hash::make($formFields['password']),
            'address' => $formFields['address'],
            'phone_number' => $formFields['phone_number']
        ]);

        $accessTokenModel = JwtToken::createToken($user->id, true);
        $refreshTokenModel = JwtToken::createToken($user->id, false);

        $tService = new TokenService();
        $tokens = $tService->createToken($user, $accessTokenModel, $refreshTokenModel);

        return response([
            'status' => 'success',
            'message' => 'admin account created successfully!',
            'data' => [
                ...$user->toArray(),
                'access_token' => $tokens->accessToken,
                'refresh_token' => $tokens->refreshToken
            ]
        ], \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }

    private function storeToken() {

    }
}
