#!/bin/bash
set -e

echo "======================================"
echo "Starting Laravel application..."
echo "======================================"

# Clear old caches
echo "Clearing Laravel caches..."
php artisan optimize:clear || true

# Run migrations if the database is available.
# If it isn't, don't stop the container.
echo "Running migrations..."
php artisan migrate --force || echo "Skipping migrations (database unavailable)."

# Cache configuration (don't fail startup if something goes wrong)
echo "Caching Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "======================================"
echo "Starting Apache..."
echo "======================================"

exec apache2-foreground