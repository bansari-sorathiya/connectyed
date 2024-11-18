<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // For API requests, return null to avoid redirection
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // For web requests, redirect to login page
        return route('login');
    }

    /**
     * Handle unauthenticated requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            throw new AuthenticationException(
                'Unauthenticated.', $guards, $this->redirectTo($request)
            );
        }

        redirect()->guest($this->redirectTo($request));
    }
}
