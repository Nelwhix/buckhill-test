<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
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

class TokenVO {
    public function __construct(public string $accessToken, public string $refreshToken){}
}

class TokenService
{
    private InMemory $accessSigner;
    private InMemory $refreshSigner;
    private Sha256 $algo;

    public function __construct() {
        $this->accessSigner = InMemory::plainText(env('JWT_ACCESS_SECRET_KEY'));
        $this->refreshSigner = InMemory::plainText(env('JWT_REFRESH_SECRET_KEY'));
        $this->algo = new Sha256();
    }
    public function createToken(User $user): TokenVO {
        $builder = new Builder(new JoseEncoder(), ChainedFormatter::default());

        $now = CarbonImmutable::now();
        $expiry1 = CarbonImmutable::now()->addMinutes(5);
        $expiry2 = CarbonImmutable::now()->addDay();

        $accessToken = $builder
            ->issuedBy(env('APP_URL'))
            ->issuedAt($now)
            ->expiresAt($expiry1)
            ->withClaim('user_uuid', $user->uuid)
            ->getToken($this->algo, $this->accessSigner)
            ->toString();

        $refreshToken = $builder
            ->issuedBy(env('APP_URL'))
            ->issuedAt($now)
            ->expiresAt($expiry2)
            ->withClaim('user_uuid', $user->uuid)
            ->getToken($this->algo, $this->refreshSigner)
            ->toString();

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


    public function parseAndValidate(string $token, bool $isAccessToken): Token|null {
        $parsedToken = $this->parseToken($token);
        if ($parsedToken === null) {
            return null;
        }

        $validator = new Validator();
        $clock = new SystemClock(new \DateTimeZone(env("APP_TIMEZONE")));

        try {
            $validator->assert($parsedToken, new IssuedBy(env('APP_URL')));
            if ($isAccessToken) {
                $interval = CarbonInterval::minutes('5');
                $validator->assert($parsedToken, new SignedWith($this->algo, $this->accessSigner));
            } else {
                $interval = CarbonInterval::day();
                $validator->assert($parsedToken, new SignedWith($this->algo, $this->refreshSigner));
            }
            $validator->assert($parsedToken, new StrictValidAt($clock, $interval));

        } catch (RequiredConstraintsViolated $e) {
            Log::error($e->violations());
            return null;
        }
        assert($parsedToken instanceof UnencryptedToken);
        $userUuid = $parsedToken->claims()->get('user_uuid');
        if ($userUuid === null || strlen($userUuid) !== 36) {
            return null;
        }


        return $parsedToken;
    }
}
