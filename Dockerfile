FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Redis PHP extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer (copy from official image)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy everything into container
COPY . .

# Install PHP dependencies (Laravel vendor)
RUN composer install --no-interaction --optimize-autoloader


FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Redis PHP extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer (copy from official image)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy everything into container
COPY . .

# Install PHP dependencies (Laravel vendor)
RUN composer install --no-interaction --optimize-autoloader

# Clear Laravel caches
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear

# Expose port 8000 and run Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]


