<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyStripeWebhook
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->header('Stripe-Signature')) {
            Log::error('Missing Stripe signature');
            return response()->json(['error' => 'Invalid request'], 400);
        }

        return $next($request);
    }
}