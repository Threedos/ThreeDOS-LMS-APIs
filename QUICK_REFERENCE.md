# Redis Caching Quick Reference

## 🚀 Quick Start

### Check Redis Status
```bash
redis-cli ping
# Expected: PONG
```

### View Cache Statistics
```bash
curl -X GET http://localhost/api/cache/stats \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test Caching Script
```bash
./test-redis-cache.sh
```

## 📋 Common Commands

### Clear All Endpoint Cache
```bash
curl -X DELETE http://localhost/api/cache/endpoint \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Clear Specific Resource Cache
```bash
# Clear users cache
curl -X DELETE http://localhost/api/cache/resource \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"resource": "users"}'

# Available resources:
# users, councils, tasks, sessions, attendances, roles, task-submissions
```

### Clear User-Specific Cache
```bash
curl -X DELETE http://localhost/api/cache/user/123 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔍 Redis CLI Commands

### View All Keys
```bash
redis-cli KEYS "*"
```

### View Keys by Pattern
```bash
# Rate limiting keys
redis-cli KEYS "rate_limit:*"

# Endpoint cache keys
redis-cli KEYS "endpoint_cache:*"

# User cache keys
redis-cli KEYS "user:*"

# Council cache keys
redis-cli KEYS "council:*"
```

### Get Specific Key
```bash
redis-cli GET "user:123"
```

### Check TTL (Time To Live)
```bash
redis-cli TTL "user:123"
# Returns seconds until expiration
```

### Delete Specific Key
```bash
redis-cli DEL "user:123"
```

### Delete All Keys (⚠️ DANGER)
```bash
redis-cli FLUSHDB
```

### Count Total Keys
```bash
redis-cli DBSIZE
```

### View Redis Memory Usage
```bash
redis-cli INFO memory
```

## 📊 Cache Key Patterns

### Individual Resources
```
user:{id}
council:{id}
task:{id}
role:{id}
session:{id}
attendance:{id}
task_submission:{id}
```

### Resource Lists
```
users:page_{pageIndex}:size_{pageSize}:search_{search}
councils:page_{pageIndex}:size_{pageSize}:search_{search}
tasks:page_{pageIndex}:size_{pageSize}:search_{search}:filter_{filter}
roles:all
sessions:council_{councilId}:page_{pageIndex}:size_{pageSize}:search_{search}
attendances:council_{councilId}:page_{pageIndex}:size_{pageSize}
task_submissions:user_{userId}:page_{pageIndex}:size_{pageSize}
task_submissions:council_{councilId}:page_{pageIndex}:size_{pageSize}
```

### System Keys
```
rate_limit:user:{userId}:minute
rate_limit:user:{userId}:hour
rate_limit:user:{userId}:day
endpoint_cache:user:{userId}:uri:{hash}:query:{hash}
```

## 🛠️ Troubleshooting

### Cache Not Working?

1. **Check Redis Connection**
   ```bash
   redis-cli ping
   ```

2. **Check Laravel Cache Config**
   ```bash
   php artisan config:cache
   ```

3. **Check Environment Variables**
   ```bash
   cat .env | grep CACHE
   cat .env | grep REDIS
   ```

4. **View Laravel Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Cache Not Clearing?

1. **Manual Clear via Redis**
   ```bash
   redis-cli FLUSHDB
   ```

2. **Clear via API**
   ```bash
   curl -X DELETE http://localhost/api/cache/endpoint \
     -H "Authorization: Bearer YOUR_TOKEN"
   ```

3. **Restart Redis**
   ```bash
   sudo systemctl restart redis
   ```

### High Memory Usage?

1. **Check Redis Memory**
   ```bash
   redis-cli INFO memory | grep used_memory_human
   ```

2. **View Largest Keys**
   ```bash
   redis-cli --bigkeys
   ```

3. **Reduce TTL** (edit controllers)
   ```php
   $this->cacheService->remember($key, 1800, function() {
       // 30 minutes instead of 1 hour
   });
   ```

## 📈 Performance Testing

### Test Cache Hit/Miss

1. **First Request (MISS)**
   ```bash
   curl -i http://localhost/api/users \
     -H "Authorization: Bearer YOUR_TOKEN"
   # Look for: X-Cache-Status: MISS
   ```

2. **Second Request (HIT)**
   ```bash
   curl -i http://localhost/api/users \
     -H "Authorization: Bearer YOUR_TOKEN"
   # Look for: X-Cache-Status: HIT
   ```

3. **Compare Response Times**
   ```bash
   # First request (uncached)
   time curl http://localhost/api/users \
     -H "Authorization: Bearer YOUR_TOKEN"
   
   # Second request (cached)
   time curl http://localhost/api/users \
     -H "Authorization: Bearer YOUR_TOKEN"
   ```

## 🔧 Configuration

### Adjust Cache TTL

Edit `routes/api.php`:
```php
// 1 hour (3600 seconds)
Route::apiResource('users', UserController::class)
    ->middleware('cache.response:3600');

// 30 minutes (1800 seconds)
Route::apiResource('users', UserController::class)
    ->middleware('cache.response:1800');

// 5 minutes (300 seconds)
Route::apiResource('users', UserController::class)
    ->middleware('cache.response:300');
```

### Disable Caching for Specific Route

Remove middleware:
```php
Route::apiResource('users', UserController::class);
// No ->middleware('cache.response:3600')
```

## 📚 Documentation Files

- **`REDIS_CACHING.md`** - Complete implementation guide
- **`CACHING_SUMMARY.md`** - Implementation summary
- **`CACHING_ARCHITECTURE.md`** - System architecture
- **`QUICK_REFERENCE.md`** - This file

## 🎯 Best Practices

1. **Monitor Cache Hit Ratio**
   - Aim for >70% hit ratio
   - Use `/api/cache/stats` endpoint

2. **Set Appropriate TTL**
   - Frequently changing data: 5-15 minutes
   - Stable data: 1 hour
   - Static data: 24 hours

3. **Always Invalidate on Mutations**
   - Clear cache after CREATE/UPDATE/DELETE
   - Use `clearResourceCache()` method

4. **Test Cache Behavior**
   - Verify cache hits/misses
   - Check response times
   - Monitor Redis memory

5. **Handle Failures Gracefully**
   - CacheService has try-catch blocks
   - Falls back to database if Redis fails

## 🚨 Important Notes

- ✅ All GET requests are cached automatically
- ✅ POST/PUT/DELETE requests clear related caches
- ✅ Cache keys include user ID for security
- ✅ Rate limiting uses separate Redis keys
- ⚠️ Don't cache sensitive data without encryption
- ⚠️ Monitor Redis memory usage regularly
- ⚠️ Clear cache after major data migrations

## 📞 Support

If you encounter issues:
1. Check Redis connection: `redis-cli ping`
2. Review logs: `tail -f storage/logs/laravel.log`
3. Clear all cache: `redis-cli FLUSHDB`
4. Restart services: `sudo systemctl restart redis`
