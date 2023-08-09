<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;

class RedisRateLimiting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 10, $decayMinutes = 1): Response
    {
        $ip = $request->ip();
        $key = "ratelimit:{$ip}";

        if (Redis::exists($key)) {
            if (Redis::incr($key) > $maxAttempts) {
                return response('Rate limit exceeded', 429);
            }
        } else {
            Redis::setex($key, $decayMinutes * 60, 1);
        }

        return $next($request);
    }
}
