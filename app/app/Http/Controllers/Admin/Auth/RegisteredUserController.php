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
     *     tags={"Admin"},
     *     path="/api/v1/admin/create",
     *     summary="Create an Admin account",
     *     @OA\RequestBody(
     *          required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                  required={"first_name", "last_name", "email", "password", "password_confirmation", "address", "phone_number", "avatar"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="User first name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="last_name",
     *                      description="User last name",
     *                      type="string"
     *                  ),
     *                 @OA\Property(
     *                      property="email",
     *                      description="User email",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="password",
     *                      description="User password",
     *                      type="string"
     *                  ),
     *                  @OA\Property  (
     *                      property="password_confirmation",
     *                      description="Password confirmation",
     *                      type="string"
     *                  ),
     *                   @OA\Property  (
     *                      description="User address",
     *                      property="address",
     *                      type="string"
     *                  ),
     *                  @OA\Property  (
     *                      description="User phone number",
     *                      property="phone_number",
     *                      type="string"
     *                  ),
     *                  @OA\Property  (
     *                      description="User profile picture UUID",
     *                      property="avatar",
     *                      type="string"
     *                  ),
     *                  @OA\Property  (
     *                      description="marketing",
     *                      property="marketing",
     *                      type="string"
     *                  ),
     *                 example={"first_name": "Bruno", "last_name": "Isioma", "email": "brunoisioma1@gmail.com", "password": "test123", "password_confirmation": "test123", "address": "Ba sing se", "phone_number": "011111"}
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *          response=201,
     *          description="Register Successfully"
     *       ),

     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=500, description="Server Error"),
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
            'phone_number' => $formFields['phone_number'],
            'avatar' => $formFields['avatar'],
            'is_marketing' => $request->has('marketing') ? 1 : 0
        ]);

        return response([
            'status' => 'success',
            'message' => 'admin account created successfully!',
            'data' => [
                ...$user->toArray(),
            ]
        ], \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
    }
}
