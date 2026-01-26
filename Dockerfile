# Base image with PHP 8.4 and Apache
FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Redis
RUN pecl install redis && docker-php-ext-enable redis

# Fix MPM configuration - disable all MPMs first, then enable only prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && rm -f /etc/apache2/mods-enabled/mpm_* \
    && a2enmod mpm_prefork \
    && a2enmod rewrite headers

# Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html
COPY . .

# Install Laravel dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache configuration for Laravel
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

# Expose container port
EXPOSE 80