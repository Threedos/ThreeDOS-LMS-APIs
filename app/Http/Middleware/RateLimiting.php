<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimiting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            $user = $request->user();
            if (!$user) {
                return $next($request);
            }
            
            $limit = $user->rate_limit;
            $remaining = $user->rate_limit_remaining;
            $reset = $user->rate_limit_reset;
            
            if ($remaining <= 0) {
                return response()->json([
                    'message' => 'Rate limit exceeded',
                ], 429);
            }

            $user->rate_limit_remaining = $remaining - 1;
            $user->rate_limit_reset = $reset + 60;
            $user->save();

            return $next($request);
        }
    }

