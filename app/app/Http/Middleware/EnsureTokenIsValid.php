<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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


        return $next($request);
    }
}
