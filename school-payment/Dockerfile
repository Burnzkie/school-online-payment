# =============================================================================
# Stage 1: Composer dependencies (isolated, lightweight)
# =============================================================================
FROM composer:2 AS composer

WORKDIR /app

# Copy only dependency files first → excellent layer caching
COPY composer.json composer.lock* ./

# Install production dependencies only – skip discovery/upgrade here
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

# Set working directory
WORKDIR /var/www/html

# Install system dependencies + PHP extensions (only what's truly needed)
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

# Copy Composer dependencies from previous stage
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=composer /app/composer.* /var/www/html/

# Copy the rest of the application code
COPY . .

# Create minimal .env and strip any APP_KEY placeholder so Render's env var takes effect at runtime
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

# Set Laravel/Filament production environment
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

# Prepare Laravel directories early (prevents permission/cache issues later)
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Set Apache to serve from Laravel's /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port (most platforms like Render expect 80)
EXPOSE 80

# At startup: inject APP_KEY from Render env var into .env, then boot
CMD sed -i '/^APP_KEY=/d' .env \
    && echo "APP_KEY=${APP_KEY}" >> .env \
    && php artisan config:clear --ansi \
    && php artisan view:clear --ansi \
    && php artisan route:clear --ansi \
    && php artisan package:discover --ansi \
    && php artisan filament:upgrade --ansi \
    && php artisan filament:optimize --ansi \
    && apache2-foreground