<?php

namespace App\Services;

final class TokenVO {
    public function __construct(public string $accessToken, public string $refreshToken){}
}
