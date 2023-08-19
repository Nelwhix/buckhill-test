<?php

namespace App\Services;

use Lcobucci\JWT\UnencryptedToken;

final class TokenVO {
    public function __construct(public UnencryptedToken $accessToken, public UnencryptedToken $refreshToken){}
}
