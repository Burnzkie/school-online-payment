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

COPY . .

# Build a clean .env — strip APP_KEY entirely so no placeholder survives into the image
RUN cp -n .env.example .env || true \
    && sed -i '/^APP_KEY=/d' .env \
    && echo "APP_ENV=production" >> .env \
    && echo "APP_DEBUG=false" >> .env \
    && echo "LOG_CHANNEL=stderr" >> .env \
    && echo "DB_CONNECTION=mysql" >> .env \
    && echo "DB_HOST=mysql" >> .env \
    && echo "DB_PORT=3306" >> .env \
    && echo "DB_DATABASE=laravel" >> .env \
    && echo "DB_USERNAME=root" >> .env \
    && echo "DB_PASSWORD=" >> .env

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

# At startup:
# 1. Remove any leftover APP_KEY line (including placeholders like "php artisan key:generate --show")
# 2. Write the real APP_KEY from Render's environment variable
# 3. Fail loudly if APP_KEY is empty so the problem is obvious
CMD if [ -z "${APP_KEY}" ]; then \
        echo "ERROR: APP_KEY environment variable is not set!" && exit 1; \
    fi \
    && sed -i '/^APP_KEY=/d' .env \
    && echo "APP_KEY=${APP_KEY}" >> .env \
    && php artisan config:clear --ansi \
    && php artisan view:clear --ansi \
    && php artisan route:clear --ansi \
    && php artisan package:discover --ansi \
    && php artisan filament:upgrade --ansi \
    && php artisan filament:optimize --ansi \
    && apache2-foreground