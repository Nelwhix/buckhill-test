<?php

use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;

it('creates a token service instance', function () {
    $tokenService = new TokenService();

    expect($tokenService)->toBeInstanceOf(TokenService::class);
});

it('generates access token and refresh token', function () {
    $tokenService = new TokenService();

    $user = User::factory()->create();
    $accessTokenModel = JwtToken::factory()->create([
        'user_id' => $user->id,
        'token_title' => 'access token',
        'expires_at' => now()->addMinutes(5)
    ]);

    $refreshTokenModel = JwtToken::factory()->create([
        'user_id' => $user->id,
        'token_title' => 'refresh token',
        'expires_at' => now()->addDay()
    ]);
   $tokenVO = $tokenService->createToken($user, $accessTokenModel, $refreshTokenModel);

   expect($tokenVO->accessToken)->toBeString()
       ->and($tokenVO->refreshToken)->toBeString();
});

it('parses and verifies the jwt', function () {

});
