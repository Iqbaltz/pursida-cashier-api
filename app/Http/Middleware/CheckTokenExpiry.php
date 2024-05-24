<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckTokenExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenTTL = auth()->factory()->getTTL() * 60;
        $tokenExpiry = JWTAuth::parseToken()->getPayload()->get('exp');
        $currentTime = time();

        $expiresIn = $tokenExpiry - $currentTime;

        if ($expiresIn < 3600) {
            try {
                $newToken = auth()->refresh();
                $response = $next($request);
                $response->headers->set('Authorization', 'Bearer ' . $newToken);
                return $response;
            } catch (\Exception $e) {
                return response()->json(['error' => 'Token refresh failed'], 401);
            }
        }

        return $next($request);
    }
}
