# =============================================================================
# Stage 1: Composer dependencies
# =============================================================================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# =============================================================================
# Stage 2: Production image with PHP + Apache + PostgreSQL
# =============================================================================
FROM php:8.3-apache

WORKDIR /var/www/html

# Install system dependencies and PostgreSQL driver
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        mbstring \
        exif \
        pcntl \
        bcmath \
        intl \
        xml \
        dom \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy composer dependencies from stage 1
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=composer /app/composer.* /var/www/html/

# Copy application files
COPY . .

# Create minimal .env file
RUN rm -f .env \
    && echo "APP_NAME=Laravel" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "APP_URL=http://localhost" >> .env \
    && echo "APP_KEY=" >> .env \
    && echo "LOG_CHANNEL=stderr" >> .env \
    && echo "LOG_LEVEL=debug" >> .env \
    && echo "DB_CONNECTION=pgsql" >> .env \
    && echo "SESSION_DRIVER=database" >> .env \
    && echo "SESSION_LIFETIME=120" >> .env \
    && echo "QUEUE_CONNECTION=database" >> .env \
    && echo "CACHE_STORE=database" >> .env \
    && echo "FILESYSTEM_DISK=local" >> .env \
    && echo "BROADCAST_CONNECTION=log" >> .env

# Set proper permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Configure Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

# Startup command
CMD php artisan key:generate --force --no-interaction \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && apache2-foreground