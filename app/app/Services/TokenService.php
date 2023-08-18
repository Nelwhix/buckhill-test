<?php

namespace App\Services;

use App\Models\JwtToken;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;

final class TokenService
{
    private Key $signingKey;
    private Signer $signer;
    private Builder $tokenBuilder;

    public function __construct() {
       $config = Configuration::forAsymmetricSigner(
           new Signer\Rsa\Sha256(),
           InMemory::file(storage_path('app/private_key.pem')),
           InMemory::base64Encoded(env('JWT_ACCESS_PUBLIC_KEY'))
       );

       $this->signer = $config->signer();
       $this->tokenBuilder = $config->builder(ChainedFormatter::default());
       $this->signingKey = $config->signingKey();
    }

    private function generateToken(JwtToken $tokenModel, User $user): string  {
        $iss = CarbonImmutable::parse($tokenModel->created_at);
        $exp = CarbonImmutable::parse($tokenModel->expires_at);

        return $this->tokenBuilder
            ->issuedBy(env('APP_URL'))
            ->issuedAt($iss)
            ->expiresAt($exp)
            ->withClaim('user_uuid', $user->uuid)
            ->withClaim('token_uuid', $tokenModel->unique_id)
            ->getToken($this->signer, $this->signingKey)
            ->toString();
    }
    public function createToken(User $user, JwtToken $accessTokenModel, JwtToken $refreshTokenModel): TokenVO {
        $accessToken = $this->generateToken($accessTokenModel, $user);
        $refreshToken = $this->generateToken($refreshTokenModel, $user);

        return new TokenVO($accessToken, $refreshToken);
    }

    private function parseToken(string $token): Token|null {
        $parser = new Parser(new JoseEncoder());

        try {
            if ($token == "") return null;

            $parsedToken = $parser->parse($token);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            Log::error($e->getMessage());
            return null;
        }

        return $parsedToken;
    }


//    public function parseAndValidate(string $token, bool $isAccessToken): Token|null {
//        $parsedToken = $this->parseToken($token);
//        if ($parsedToken === null) {
//            return null;
//        }
//
//        $validator = new Validator();
//        $clock = new SystemClock(new \DateTimeZone(env("APP_TIMEZONE")));
//
//        try {
//            $validator->assert($parsedToken, new IssuedBy(env('APP_URL')));
//            if ($isAccessToken) {
//                $interval = CarbonInterval::minutes('5');
//                $validator->assert($parsedToken, new SignedWith($this->algo, $this->accessSigner));
//            } else {
//                $interval = CarbonInterval::day();
//                $validator->assert($parsedToken, new SignedWith($this->algo, $this->refreshSigner));
//            }
//            $validator->assert($parsedToken, new StrictValidAt($clock, $interval));
//
//        } catch (RequiredConstraintsViolated $e) {
//            Log::error($e->violations());
//            return null;
//        }
//        assert($parsedToken instanceof UnencryptedToken);
//        $userUuid = $parsedToken->claims()->get('user_uuid');
//        if ($userUuid === null || strlen($userUuid) !== 36) {
//            return null;
//        }
//
//
//        return $parsedToken;
//    }
}
