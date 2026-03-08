# =============================================================================
# Stage 1: Composer dependencies (isolated, lightweight)
# =============================================================================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock* ./

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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
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

COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=composer /app/composer.* /var/www/html/

# Copy everything EXCEPT .env (add .env to your .dockerignore too)
COPY . .

# Delete any .env that was copied in, then build a clean one from scratch
# APP_KEY is intentionally left out — it will be injected at runtime from Render's env var
RUN rm -f .env \
    && echo "APP_NAME=Laravel" > .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "APP_URL=http://localhost" >> .env \
    && echo "APP_KEY=" >> .env \
    && echo "LOG_CHANNEL=stderr" >> .env \
    && echo "LOG_LEVEL=debug" >> .env \
    && echo "DB_CONNECTION=mysql" >> .env \
    && echo "DB_HOST=mysql" >> .env \
    && echo "DB_PORT=3306" >> .env \
    && echo "DB_DATABASE=laravel" >> .env \
    && echo "DB_USERNAME=root" >> .env \
    && echo "DB_PASSWORD=" >> .env \
    && echo "SESSION_DRIVER=database" >> .env \
    && echo "SESSION_LIFETIME=120" >> .env \
    && echo "QUEUE_CONNECTION=database" >> .env \
    && echo "CACHE_STORE=database" >> .env \
    && echo "FILESYSTEM_DISK=local" >> .env \
    && echo "BROADCAST_CONNECTION=log" >> .env

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

RUN mkdir -p storage/framework/{cache,sessions,views} \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

# At startup: inject real APP_KEY and DB config from Render env vars, then boot
CMD if [ -z "${APP_KEY}" ]; then \
        echo "ERROR: APP_KEY environment variable is not set on Render!" && exit 1; \
    fi \
    && sed -i '/^APP_KEY=/d' .env \
    && echo "APP_KEY=${APP_KEY}" >> .env \
    && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env \
    && sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env \
    && sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env \
    && php artisan config:clear --ansi \
    && php artisan view:clear --ansi \
    && php artisan route:clear --ansi \
    && php artisan package:discover --ansi \
    && php artisan filament:upgrade --ansi \
    && php artisan filament:optimize --ansi \
    && apache2-foreground