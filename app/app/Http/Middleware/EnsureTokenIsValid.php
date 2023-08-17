<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authPayLoad = $request->Header('Authorization');
        if ($authPayLoad === null) {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (trim($authPayLoad) === "") {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tokenPayload = explode(" ", $authPayLoad);
        $tokenService = new TokenService();
        $parsedToken = $tokenService->parseAndValidate($tokenPayload[1], true);

        if ($parsedToken === null) {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
        assert($parsedToken instanceof UnencryptedToken);
        $userUuid = $parsedToken->claims()->get('user_uuid');
        $user = User::whereUuid($userUuid)->firstOrFail();

        if ($request->is('admin/*')) {
            return response([
                'status' => 'failed',
                'message' => 'unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
