#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for database
php artisan db:show 2>/dev/null || sleep 5

echo "Running migrations..."
php artisan migrate --force

echo "Optimizing Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
