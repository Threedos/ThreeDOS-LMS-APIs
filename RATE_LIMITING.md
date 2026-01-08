# Rate Limiting Implementation

## Overview
This document describes the custom rate limiting implementation for the ThreeDOS APIs project.

## Features

### 1. **Multi-Tier Rate Limiting**
The system implements three tiers of rate limiting:
- **Minute**: Default 60 requests/minute (configurable per user via `rate_limit` field)
- **Hour**: Default 1,000 requests/hour (configurable per user via `hourly_limit` field)
- **Day**: Default 10,000 requests/day (configurable per user via `daily_limit` field)

### 2. **Per-User Limits**
Each authenticated user has their own rate limit counters, allowing for:
- Individual rate limit customization
- Isolation between users
- No shared limit pools

### 3. **Redis-First with Fallback**
- **Primary**: Uses Redis with Lua scripts for atomic operations (prevents race conditions)
- **Fallback**: Uses Laravel Cache when Redis is unavailable (useful for testing)

### 4. **Standard HTTP Headers**
All responses include standard rate limit headers:
- `X-RateLimit-Limit`: Maximum requests allowed in the current window
- `X-RateLimit-Remaining`: Requests remaining in the current window
- `X-RateLimit-Reset`: Unix timestamp when the rate limit resets

### 5. **429 Rate Limit Exceeded Response**
When limits are exceeded, the API returns:
```json
{
  "message": "Rate limit exceeded",
  "tier": "minute|hour|day",
  "limit": 60,
  "retry_after": 45
}
```

With headers:
- `Retry-After`: Seconds until the user can retry
- All standard rate limit headers

## Implementation Details

### Middleware: `RateLimiting.php`
Located at: `app/Http/Middleware/RateLimiting.php`

**Key Methods:**
- `handle()`: Main middleware entry point, checks all tiers
- `checkLimit()`: Checks a specific tier using Redis Lua script
- `fallbackCheck()`: Cache-based fallback when Redis is unavailable
- `rateLimitResponse()`: Generates 429 response with proper headers
- `addRateLimitHeaders()`: Adds rate limit headers to successful responses

### Redis Lua Script
The implementation uses a Lua script for atomic operations:
```lua
local key = KEYS[1]
local limit = tonumber(ARGV[1])
local window = tonumber(ARGV[2])
local current = redis.call('GET', key)

if current == false then
    redis.call('SET', key, 1, 'EX', window)
    return {1, limit - 1, window}
end

current = tonumber(current)
if current >= limit then
    local ttl = redis.call('TTL', key)
    return {0, 0, ttl}
end

redis.call('INCR', key)
local remaining = limit - current - 1
local ttl = redis.call('TTL', key)
return {1, remaining, ttl}
```

This ensures:
- No race conditions between check and increment
- Accurate remaining count
- Proper TTL tracking

### Route Configuration
In `routes/api.php`, the middleware is applied to all authenticated routes:
```php
Route::middleware(['auth:api', \App\Http\Middleware\RateLimiting::class])->group(function () {
    // All protected routes
});
```

## User Model Fields

To customize rate limits per user, add these fields to the `users` table:
- `rate_limit` (integer, nullable): Requests per minute (default: 60)
- `hourly_limit` (integer, nullable): Requests per hour (default: 1000)
- `daily_limit` (integer, nullable): Requests per day (default: 10000)

## Testing

### Test Files
1. **RateLimitTest.php**: Basic rate limiting enforcement test
2. **RateLimitComprehensiveTest.php**: Comprehensive test suite covering:
   - Header presence and accuracy
   - Remaining count decrementing
   - Blocking after limit exceeded
   - Retry-After header
   - Unauthenticated bypass
   - Separate user limits
   - Multi-tier enforcement
   - Default values

### Running Tests
```bash
# Run all rate limit tests
php artisan test --filter RateLimit

# Run specific test file
php artisan test tests/Feature/RateLimitTest.php
php artisan test tests/Feature/RateLimitComprehensiveTest.php
```

### Test Results
All 9 tests pass with 36 assertions:
- ✓ rate limit headers are present
- ✓ rate limit remaining decrements
- ✓ rate limit blocks after limit exceeded
- ✓ rate limit response includes retry after
- ✓ unauthenticated requests bypass rate limiting
- ✓ different users have separate rate limits
- ✓ multi tier rate limiting hour limit
- ✓ rate limit uses default values when not set
- ✓ custom rate limiting enforcement

## Key Design Decisions

### 1. **Unauthenticated Requests Bypass**
Unauthenticated requests are not rate limited because:
- They're already protected by authentication middleware
- No user context to track limits against
- Prevents unnecessary complexity

### 2. **UUID Support**
The middleware supports string-based user IDs (UUIDs) as used in this project.

### 3. **Graceful Degradation**
If Redis fails, the system automatically falls back to Laravel's Cache system, ensuring the API remains functional.

### 4. **Atomic Operations**
Using Lua scripts in Redis ensures that checking and incrementing the counter happens atomically, preventing race conditions in high-concurrency scenarios.

## Future Enhancements

Potential improvements:
1. **IP-based rate limiting** for unauthenticated requests
2. **Sliding window** algorithm instead of fixed windows
3. **Rate limit exemptions** for specific roles (e.g., admins)
4. **Dynamic rate limit adjustment** based on system load
5. **Rate limit analytics** and monitoring dashboard

## Troubleshooting

### Redis Connection Issues
If Redis is not available, the middleware automatically falls back to Cache. Check:
```bash
# Test Redis connection
php artisan tinker
>>> Redis::ping()
```

### Rate Limits Not Working
1. Ensure middleware is registered in routes
2. Check user is authenticated
3. Verify Redis/Cache is working
4. Check user model has rate limit fields

### Tests Failing
1. Run `php artisan config:clear`
2. Ensure database is migrated: `php artisan migrate:fresh`
3. Check Redis is running (or use array cache driver for testing)
