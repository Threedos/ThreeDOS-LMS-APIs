#!/bin/bash

echo "==================================="
echo "Redis Caching Test Script"
echo "==================================="
echo ""

# Test 1: Check Redis connection
echo "1. Testing Redis connection..."
if redis-cli ping > /dev/null 2>&1; then
    echo "   ✅ Redis is running"
else
    echo "   ❌ Redis is not running"
    exit 1
fi
echo ""

# Test 2: Check current Redis keys
echo "2. Current Redis keys:"
KEY_COUNT=$(redis-cli DBSIZE | grep -oP '\d+')
echo "   Total keys: $KEY_COUNT"
echo ""

# Test 3: Show keys by pattern
echo "3. Keys by category:"
echo "   Rate limiting keys:"
redis-cli --scan --pattern "rate_limit:*" | wc -l | xargs echo "     Count:"

echo "   Endpoint cache keys:"
redis-cli --scan --pattern "endpoint_cache:*" | wc -l | xargs echo "     Count:"

echo "   User cache keys:"
redis-cli --scan --pattern "user:*" | wc -l | xargs echo "     Count:"

echo "   Users list cache keys:"
redis-cli --scan --pattern "users:*" | wc -l | xargs echo "     Count:"
echo ""

# Test 4: Redis memory usage
echo "4. Redis memory usage:"
redis-cli INFO memory | grep "used_memory_human" | cut -d: -f2 | xargs echo "   Memory used:"
echo ""

# Test 5: Sample some keys
echo "5. Sample Redis keys (first 10):"
redis-cli --scan | head -10 | while read key; do
    TTL=$(redis-cli TTL "$key")
    echo "   - $key (TTL: ${TTL}s)"
done
echo ""

echo "==================================="
echo "Test Complete!"
echo "==================================="
