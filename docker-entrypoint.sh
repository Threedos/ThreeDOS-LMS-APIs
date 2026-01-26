#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for MySQL to be ready
until php -r "new PDO('mysql:host=db;dbname=laravel', 'root', 'root');" 2>/dev/null; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "MySQL is ready."

# Run migrations safely (create missing tables only)
php artisan migrate --force || echo "Migrations already applied"

# Seed database
php artisan db:seed --force || echo "Seeding failed or already done"

# Clear and cache configs
echo "Optimizing application..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Apache runs automatically in this image
echo "Starting Apache..."
exec apache2-foreground
