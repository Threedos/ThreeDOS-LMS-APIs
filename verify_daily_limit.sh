#!/bin/bash

BASE_URL="http://127.0.0.1:8000/api"
EMAIL="vp@threedos.local"
PASSWORD="password"

# 1. Login
echo "Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\": \"$EMAIL\", \"password\": \"$PASSWORD\"}")

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
  echo "Login failed."
  exit 1
fi

echo "Login successful."

# 2. Test Daily Limit (3 requests, limit is 2)
echo "Testing Daily Limit (Limit: 2)..."
for i in {1..3}; do
  echo "Request $i..."
  RESPONSE=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X POST "$BASE_URL/ai-chat" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"message": "Daily limit test"}')
  echo "$RESPONSE"
done
