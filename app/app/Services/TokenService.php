<?php declare(strict_types=1);

namespace App\Services;

use App\Models\JwtToken;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Validator;

final class TokenService
{
    private Key $signingKey;
    private Key $verifier;
    private Signer $signer;
    private \Lcobucci\JWT\Builder $tokenBuilder;
    private \Lcobucci\JWT\Parser $parser;
    private Validator $validator;

    public function __construct() {
        $privateKeyPath = storage_path('app/private_key.pem');
        $publicKeyPath = storage_path('app/public_key.pem');
        if ($privateKeyPath === "" || $publicKeyPath === "") {
            throw new \Exception("Could not create Token Service instance");
        }
       $config = Configuration::forAsymmetricSigner(
           new Signer\Rsa\Sha256(),
           InMemory::file($privateKeyPath),
           InMemory::file($publicKeyPath)
       );

       $this->signer = $config->signer();
       $this->tokenBuilder = $config->builder(ChainedFormatter::default());
       $this->signingKey = $config->signingKey();
       $this->parser = $config->parser();
       $this->validator = $config->validator();
       $this->verifier = $config->verificationKey();
    }

    private function generateToken(JwtToken $tokenModel, User $user): UnencryptedToken
    {
        $iss = CarbonImmutable::parse($tokenModel->created_at);
        $exp = CarbonImmutable::parse($tokenModel->expires_at);

        return $this->tokenBuilder
            ->issuedBy(env('APP_URL'))
            ->issuedAt($iss)
            ->expiresAt($exp)
            ->withClaim('user_uuid', $user->uuid)
            ->withClaim('token_uuid', $tokenModel->unique_id)
            ->getToken($this->signer, $this->signingKey);

    }
    public function createToken(User $user, JwtToken $accessTokenModel, JwtToken $refreshTokenModel): TokenVO {
        $accessToken = $this->generateToken($accessTokenModel, $user);
        $refreshToken = $this->generateToken($refreshTokenModel, $user);

        return new TokenVO($accessToken, $refreshToken);
    }

    private function parseToken(string $token): Token {
        if ($token === "") {
            throw new \Exception("no token passed");
        };

        return $this->parser->parse($token);
    }

    private function validateToken(Token $parsedToken, bool $isAccessToken): bool {
        if (!$this->validator->validate($parsedToken, new IssuedBy(env('APP_URL')))) {
            return false;
        }

        if (!$this->validator->validate($parsedToken, new SignedWith($this->signer, $this->verifier))) {
            return false;
        }

        $exp = $parsedToken->claims()->get('exp');
        if (!($exp instanceof \DateTimeImmutable)) {
            return false;
        }
        if (now()->gt($exp)) {
            return false;
        }

        $userUuid = $parsedToken->claims()->get('user_uuid');
        if ($userUuid === null || strlen($userUuid) !== 36) {
            return false;
        }

        $tokenUuid = $parsedToken->claims()->get("token_uuid");
        if ($tokenUuid === null || strlen($tokenUuid) !== 36) {
            return false;
        }


        return true;
    }

    public function getTokenMetadata(string $token, bool $isAccessToken): Token|null {
        try {
            $parsedToken = $this->parseToken($token);
        } catch (\Exception $e) {
            return null;
        }

        $tokenIsValid = $this->validateToken($parsedToken, $isAccessToken);
        if (!$tokenIsValid) {
            return null;
        }


        return $parsedToken;
    }
}
