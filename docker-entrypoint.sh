#!/bin/bash
set -e

echo "Starting Laravel on Railway"

# Railway provides PORT env variable, default to 80 for local testing
PORT=${PORT:-80}

# Configure Apache ports
cat > /etc/apache2/ports.conf << EOF
Listen ${PORT}
<IfModule ssl_module>
    Listen 443
</IfModule>
<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF

# Configure VirtualHost
cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:${PORT}>
    ServerName localhost
    DocumentRoot /var/www/html/public
    
    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
    
    # Enable compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
    </IfModule>
</VirtualHost>
EOF

# Wait for database connection
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database at $DB_HOST..."
    MAX_TRIES=30
    COUNT=0
    
    until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null || [ $COUNT -eq $MAX_TRIES ]; do
        COUNT=$((COUNT + 1))
        echo "Database not ready, attempt $COUNT/$MAX_TRIES..."
        sleep 2
    done
    
    if [ $COUNT -eq $MAX_TRIES ]; then
        echo "⚠ Could not connect to database after $MAX_TRIES attempts"
        echo "Continuing anyway - database may not be required"
    else
        echo "✓ Database connection successful"
        
        # Run migrations
        echo "Running database migrations..."
        php artisan migrate --force || echo "⚠ Migration failed or already up to date"
        
        # Optional: Run seeders only on first deploy
        # php artisan db:seed --force || echo "⚠ Seeding skipped"
    fi
else
    echo "ℹ No database configured (DB_HOST not set)"
fi

# Clear all caches first (important for config changes)
echo "Clearing Laravel caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Cache optimization for production
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link (suppress error if exists)
echo "Creating storage symlink..."
php artisan storage:link 2>/dev/null || echo "ℹ Storage link already exists"

# Display Laravel info
echo "================================"
echo "Laravel version: $(php artisan --version)"
echo "Environment: ${APP_ENV:-production}"
echo "Debug mode: ${APP_DEBUG:-false}"
echo "Port: ${PORT}"
echo "================================"

# Start Apache in foreground
echo "Starting Apache..."
exec apache2-foreground