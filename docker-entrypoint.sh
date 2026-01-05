#!/bin/bash
set -e

# Clear caches at runtime (Redis must be running)
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000
