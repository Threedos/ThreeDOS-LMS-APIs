#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for database to be ready (optional but helpful)
php artisan db:show 2>/dev/null || sleep 5

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache configs
echo "Optimizing application..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Laravel server
echo "Starting web server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000