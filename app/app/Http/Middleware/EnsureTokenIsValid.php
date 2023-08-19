<?php

namespace App\Http\Middleware;

use App\Models\JwtToken;
use App\Models\User;
use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function __construct(protected TokenService $tokenService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken() === null) {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $parsedToken = $this->tokenService->getTokenMetadata($request->bearerToken(), true);

        if ($parsedToken === null) {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
        assert($parsedToken instanceof UnencryptedToken);
        $userUuid = $parsedToken->claims()->get('user_uuid');
        $tokenUuid = $parsedToken->claims()->get('token_uuid');
        $user = User::where('uuid', $userUuid)->firstOrFail();
        $tokenModel = JwtToken::whereUniqueId($tokenUuid)->firstOrFail();
        $tokenModel->last_used_at = now();
        $tokenModel->save();

        if ($request->is('admin/*') && $user->is_admin === 0) {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
