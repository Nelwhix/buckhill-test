<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
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

        $tService = new TokenService();
        $token = $tService->createToken($user);

        JwtToken::create([
            'unique_id' => Str::uuid(),
            'user_id' => $user->id,
            'token_title' => 'access token',
            'expires_at' => now()->addMinutes(5),
        ]);

        JwtToken::create([
            'unique_id' => Str::uuid(),
            'user_id' => $user->id,
            'token_title' => 'refresh token',
            'expires_at' => now()->addDay(),
        ]);

        return response([
            'status' => 'success',
            'message' => 'admin account created successfully!',
            'data' => [
                ...$user->toArray(),
                'access_token' => $token->accessToken,
                'refresh_token' => $token->refreshToken
            ]
        ]);
    }
}
