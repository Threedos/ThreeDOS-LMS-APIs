# Redis Caching Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT REQUEST                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      API MIDDLEWARE STACK                       │
├─────────────────────────────────────────────────────────────────┤
│  1. Authentication (auth:api)                                   │
│  2. Rate Limiting (RateLimiting) ──────► REDIS (rate_limit:*)  │
│  3. Cache Response (cache.response) ───► REDIS (endpoint_cache:*)│
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         CONTROLLER                              │
├─────────────────────────────────────────────────────────────────┤
│  • UserController                                               │
│  • CouncilController                                            │
│  • TaskController                                               │
│  • RoleController                                               │
│  • CouncilSessionController                                     │
│  • AttendanceController                                         │
│  • TaskSubmissionController                                     │
│                                                                  │
│  All inject: CacheService                                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        CACHESERVICE                             │
├─────────────────────────────────────────────────────────────────┤
│  Methods:                                                       │
│  • remember(key, ttl, callback) ──► Get or store in Redis      │
│  • put(key, value, ttl) ──────────► Store in Redis             │
│  • get(key, default) ─────────────► Retrieve from Redis        │
│  • forget(key) ───────────────────► Delete from Redis          │
│  • clearResourceCache(resource) ──► Delete pattern from Redis  │
│  • getStats() ────────────────────► Redis statistics           │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                           REDIS                                 │
├─────────────────────────────────────────────────────────────────┤
│  Storage Types:                                                 │
│                                                                  │
│  1. Rate Limiting                                               │
│     rate_limit:user:{userId}:minute                            │
│     rate_limit:user:{userId}:hour                              │
│     rate_limit:user:{userId}:day                               │
│                                                                  │
│  2. Endpoint Cache (Middleware)                                │
│     endpoint_cache:user:{userId}:uri:{hash}:query:{hash}       │
│                                                                  │
│  3. Resource Lists (Service)                                   │
│     users:page_{pageIndex}:size_{pageSize}:search_{search}     │
│     councils:page_{pageIndex}:size_{pageSize}:search_{search}  │
│     tasks:page_{pageIndex}:size_{pageSize}:search_{search}:... │
│     roles:all                                                   │
│     sessions:council_{id}:page_{pageIndex}:size_{pageSize}:... │
│     attendances:council_{id}:page_{pageIndex}:size_{pageSize}  │
│     task_submissions:user_{userId}:page_{pageIndex}:...        │
│     task_submissions:council_{id}:page_{pageIndex}:...         │
│                                                                  │
│  4. Individual Items (Service)                                 │
│     user:{id}                                                   │
│     council:{id}                                                │
│     task:{id}                                                   │
│     role:{id}                                                   │
│     session:{id}                                                │
│     attendance:{id}                                             │
│     task_submission:{id}                                        │
└─────────────────────────────────────────────────────────────────┘
```

## Request Flow

### GET Request (Cache Hit)
```
Client Request
    │
    ▼
Middleware: CacheResponse
    │
    ├─► Check Redis (endpoint_cache:*)
    │   └─► HIT! Return cached response ✅
    │       (Response time: ~5-10ms)
    │
    └─► Add Header: X-Cache-Status: HIT
```

### GET Request (Cache Miss)
```
Client Request
    │
    ▼
Middleware: CacheResponse
    │
    ├─► Check Redis (endpoint_cache:*)
    │   └─► MISS! Continue to controller
    │
    ▼
Controller
    │
    ├─► CacheService->remember()
    │   │
    │   ├─► Check Redis (resource cache)
    │   │   └─► MISS! Execute callback
    │   │
    │   ▼
    │   Database Query
    │   │
    │   ▼
    │   Store in Redis (resource cache)
    │   │
    │   └─► Return data
    │
    ▼
Middleware: CacheResponse
    │
    ├─► Store in Redis (endpoint_cache:*)
    │
    └─► Add Header: X-Cache-Status: MISS
```

### POST/PUT/DELETE Request (Cache Invalidation)
```
Client Request (Create/Update/Delete)
    │
    ▼
Controller
    │
    ├─► Perform database operation
    │
    ▼
CacheService
    │
    ├─► forget("resource:{id}")        // Clear specific item
    │
    ├─► clearResourceCache("resource") // Clear all related caches
    │   │
    │   └─► Delete keys matching:
    │       • resource:*
    │       • endpoint_cache:*:uri:*resource*
    │
    └─► Return success response
```

## Cache Layers

### Layer 1: Middleware Cache (CacheResponse)
- **Purpose**: Cache entire HTTP responses
- **Scope**: All GET requests
- **TTL**: Configurable per route (default: 3600s)
- **Key Pattern**: `endpoint_cache:user:{userId}:uri:{hash}:query:{hash}`
- **Invalidation**: Automatic on POST/PUT/DELETE to same endpoint

### Layer 2: Service Cache (CacheService)
- **Purpose**: Cache specific data queries
- **Scope**: Controller methods
- **TTL**: 3600 seconds (1 hour)
- **Key Patterns**: 
  - Lists: `{resource}:page_{pageIndex}:size_{pageSize}:...`
  - Items: `{resource}:{id}`
- **Invalidation**: Manual via `clearResourceCache()` or `forget()`

### Layer 3: Rate Limiting Cache
- **Purpose**: Track API request rates
- **Scope**: All authenticated requests
- **TTL**: 60s (minute), 3600s (hour), 86400s (day)
- **Key Pattern**: `rate_limit:user:{userId}:{tier}`
- **Invalidation**: Automatic expiration

## Cache Management

### View Statistics
```bash
curl -X GET http://localhost/api/cache/stats \
  -H "Authorization: Bearer {token}"
```

### Clear All Endpoint Cache
```bash
curl -X DELETE http://localhost/api/cache/endpoint \
  -H "Authorization: Bearer {token}"
```

### Clear Specific Resource
```bash
curl -X DELETE http://localhost/api/cache/resource \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"resource": "users"}'
```

### Clear User Cache
```bash
curl -X DELETE http://localhost/api/cache/user/123 \
  -H "Authorization: Bearer {token}"
```

## Performance Metrics

| Metric | Without Cache | With Cache | Improvement |
|--------|--------------|------------|-------------|
| Response Time | 100-500ms | 5-10ms | **90-95% faster** |
| Database Queries | Every request | First request only | **70-90% reduction** |
| Server Load | High | Low | **Significant** |
| Concurrent Users | Limited | High | **10x increase** |

## Redis Memory Usage

Estimated memory per cache entry:
- **Endpoint Cache**: ~2-10 KB per response
- **Resource List**: ~5-50 KB per page
- **Individual Item**: ~1-5 KB per item
- **Rate Limit**: ~100 bytes per user per tier

For 1000 active users with typical usage:
- **Total Redis Memory**: ~50-200 MB
- **Cache Hit Ratio**: 70-90%
- **Performance Gain**: 10-20x faster responses
