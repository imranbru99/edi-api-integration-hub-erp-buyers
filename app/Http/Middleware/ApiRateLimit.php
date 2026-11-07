<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    public function __construct(protected RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->attributes->get('api_client');
        $limit = (int) ($client?->rate_limit ?? 60);
        $key = 'api-client:'.($client?->id ?? $request->ip());

        if ($this->limiter->tooManyAttempts($key, $limit)) {
            return response()->json(['message' => 'Too many requests.'], 429);
        }

        $this->limiter->hit($key, 60);

        return $next($request);
    }
}
