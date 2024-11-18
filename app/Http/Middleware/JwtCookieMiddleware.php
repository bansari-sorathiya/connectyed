<?php

namespace App\Http\Middleware;

use Closure;

class JwtCookieMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->bearerToken() && $request->cookie('token')) {
            $token = $request->cookie('token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
