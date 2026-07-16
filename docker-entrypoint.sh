#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for database
echo "Waiting for database connection..."
until php artisan db:show &>/dev/null; do
    echo "Database is not ready yet, sleeping 3 seconds..."
    sleep 3
done
echo "Database connection established!"

echo "Running migrations..."
php artisan migrate --force

echo "Optimizing Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
