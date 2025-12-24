<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
 public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip())
            ->response(function (Request $request, array $headers) {
                // Custom JSON response
                return response()->json([
                    'status' => 'error',
                    'code' => 429,
                    'message' => 'Rate limit exceeded. Try again later.',
                    'retry_after_seconds' => $headers['Retry-After'] ?? null
                ], 429, $headers);
            });
    });
}

}
