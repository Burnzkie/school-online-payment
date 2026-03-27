# =============================================================================
# Multi-stage build for Laravel + PostgreSQL on Render
# =============================================================================
FROM composer:2 AS composer

WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# =============================================================================
FROM php:8.3-apache

WORKDIR /var/www/html

# Install dependencies and PHP extensions for PostgreSQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev \
    libxml2-dev libicu-dev unzip git libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql zip gd mbstring exif pcntl bcmath intl xml dom \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy vendor from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=composer /app/composer.* /var/www/html/

# Copy the rest of the application
COPY . .

# Create minimal production .env
RUN rm -f .env && cat > .env << EOF
APP_NAME=Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost
LOG_CHANNEL=stderr
LOG_LEVEL=debug
DB_CONNECTION=pgsql
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log
EOF

# Set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Apache configuration
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

# Final startup command
CMD php artisan key:generate --force --no-interaction \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && apache2-foreground