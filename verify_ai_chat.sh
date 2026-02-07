#!/bin/bash

BASE_URL="http://127.0.0.1:8000/api"
EMAIL="delegate.frontend@threedos.local"
PASSWORD="password"

# 1. Login to get token
echo "Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\": \"$EMAIL\", \"password\": \"$PASSWORD\"}")

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
  echo "Login failed. Response: $LOGIN_RESPONSE"
  # Try to extract message
  MESSAGE=$(echo $LOGIN_RESPONSE | grep -o '"message":"[^"]*' | cut -d'"' -f4)
  echo "Error Message: $MESSAGE"
  exit 1
fi

echo "Login successful. Token acquired."

# 2. Test AI Chat Endpoint
echo "Testing AI Chat Endpoint..."
RESPONSE=$(curl -s -X POST "$BASE_URL/ai-chat" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello, can you help me?"}')

echo "Response: $RESPONSE"

# 3. Test Rate Limiting (6 requests)
echo "Testing Rate Limiting..."
for i in {1..6}; do
  echo "Request $i..."
  start_time=$(date +%s%N)
  curl -s -o /dev/null -w "%{http_code}\n" -X POST "$BASE_URL/ai-chat" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"message": "Rate limit test"}'
  end_time=$(date +%s%N)
  # echo "Time: $((($end_time - $start_time)/1000000)) ms"
done
