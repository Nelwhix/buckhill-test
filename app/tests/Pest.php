<?php

use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Database\Seeders\AdminSeeder;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class
)->in('Feature', 'Unit');

function generateTestTokens(): \App\Services\TokenVO
{
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

    return $tokenService->createToken($user, $accessTokenModel, $refreshTokenModel);
}

//function adminUser() {
//    test()->seed(AdminSeeder::class);
//    $user = User::where('email', 'admin@buckhill.co.uk')->first();
//
//    return test()->actingAs($user);
//}
