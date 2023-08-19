<?php

use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

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

   $accessClaims = $tokenVO->accessToken->claims();
   $refreshClaims = $tokenVO->refreshToken->claims();

   expect($accessClaims->get('token_uuid'))->toBe($accessTokenModel->unique_id)
       ->and($accessClaims->get('user_uuid'))->toBe($user->uuid)
       ->and($refreshClaims->get('token_uuid'))->toBe($refreshTokenModel->unique_id)
       ->and($refreshClaims->get('user_uuid'))->toBe($user->uuid);

});

it('parses and verifies a valid jwt', function () {
    $tokenService = new TokenService();
    $tokenVO = generateTestTokens();

    $accessToken = $tokenService->getTokenMetadata($tokenVO->accessToken->toString(), true);
    $refreshToken = $tokenService->getTokenMetadata($tokenVO->accessToken->toString(), false);

    expect($accessToken)->toBeInstanceOf(Lcobucci\JWT\Token::class)
        ->and($refreshToken)->toBeInstanceOf(Lcobucci\JWT\Token::class);
});

it('returns null for an invalid jwt', function () {
    $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
    $algorithm    = new Sha256();
    $signingKey   = InMemory::plainText(random_bytes(32));

    $now   = new DateTimeImmutable();
    $token = $tokenBuilder
        ->issuedBy('http://example.com')
        ->permittedFor('http://example.org')
        ->identifiedBy('4f1g23a12aa')
        ->issuedAt($now)
        ->canOnlyBeUsedAfter($now->modify('+1 minute'))
        ->expiresAt($now->modify('+1 hour'))
        ->withClaim('uid', 1)
        ->withHeader('foo', 'bar')
        ->getToken($algorithm, $signingKey);

    $tokenService = new TokenService();
    $accessToken = $tokenService->getTokenMetadata($token->toString(), true);

    expect($accessToken)->toBeNull();
});

it('returns null for an expired jwt', function () {
    $tokenService = new TokenService();

    $user = User::factory()->create();
    $accessTokenModel = JwtToken::factory()->create([
        'user_id' => $user->id,
        'token_title' => 'access token',
        'expires_at' => now()->addMinutes(-5)
    ]);

    $refreshTokenModel = JwtToken::factory()->create([
        'user_id' => $user->id,
        'token_title' => 'refresh token',
        'expires_at' => now()->addDay()
    ]);
    $tokenVO = $tokenService->createToken($user, $accessTokenModel, $refreshTokenModel);

    $accessToken = $tokenService->getTokenMetadata($tokenVO->accessToken->toString(), true);

    expect($accessToken)->toBeNull();
});
