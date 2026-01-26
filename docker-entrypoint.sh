#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for MySQL to be ready
until php -r "new PDO('mysql:host=db;dbname=laravel', 'root', 'root');" 2>/dev/null; do
    echo "Waiting for MySQL..."
    sleep 2
done
echo "MySQL is ready."

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migrations already applied"

# Run seeders
echo "Seeding database..."
php artisan db:seed --force || echo "Seeding already done"

# Optimize Laravel
echo "Optimizing application..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache (default in php:apache)
echo "Starting Apache..."
exec apache2-foreground
