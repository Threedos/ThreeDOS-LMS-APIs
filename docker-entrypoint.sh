#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for MySQL to be ready
until php -r "new PDO('mysql:host=db;dbname=laravel', 'root', 'root');" 2>/dev/null; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "MySQL is ready."

# Run migrations safely
echo "Running migrations..."
php artisan migrate --force || echo "Migrations already run"

# Clear and cache configs
echo "Optimizing application..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Laravel server
echo "Starting web server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
