# =============================================================================
# Stage 1: Composer dependencies (without running artisan scripts)
# =============================================================================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock* ./

# Install dependencies WITHOUT running post-autoload scripts
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs \
    --no-scripts

# =============================================================================
# Stage 2: Final production image
# =============================================================================
FROM php:8.3-apache

WORKDIR /var/www/html

# Install system dependencies + PostgreSQL support
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev \
    libxml2-dev libicu-dev unzip git libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql zip gd mbstring exif pcntl bcmath intl xml dom \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy vendor folder from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=composer /app/composer.* /var/www/html/

# Copy the entire Laravel project
COPY . .

# Create minimal .env (Laravel will prefer DATABASE_URL if set)
RUN rm -f .env && cat > .env << 'EOF'
APP_NAME=Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${RENDER_EXTERNAL_HOSTNAME:-localhost}
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

# Configure Apache to serve from /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

# Final startup - run artisan commands here (after all files are copied)
CMD php artisan key:generate --force --no-interaction \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && apache2-foreground