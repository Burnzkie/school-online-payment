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

# Install system dependencies + MySQL support
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev \
    libxml2-dev libicu-dev unzip git default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip gd mbstring exif pcntl bcmath intl xml dom \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy vendor folder from composer stage
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=composer /app/composer.* /var/www/html/

# Copy the entire Laravel project
COPY . .

# Set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Configure Apache to serve from /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create startup script
RUN cat > /usr/local/bin/start.sh << 'EOF'
#!/bin/bash
set -e

echo "Setting up .env file..."

# Create .env from .env.example if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
    echo ".env created from .env.example"
fi

cd /var/www/html

# Inject Render environment variables into .env
[ -n "$APP_NAME" ]      && sed -i "s|^APP_NAME=.*|APP_NAME=${APP_NAME}|" .env
[ -n "$APP_ENV" ]       && sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|" .env
[ -n "$APP_DEBUG" ]     && sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env
[ -n "$APP_URL" ]       && sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
[ -n "$APP_KEY" ]       && sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
[ -n "$DB_CONNECTION" ] && sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" .env
[ -n "$DB_HOST" ]       && sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST}|" .env
[ -n "$DB_PORT" ]       && sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|" .env
[ -n "$DB_DATABASE" ]   && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env
[ -n "$DB_USERNAME" ]   && sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env
[ -n "$DB_PASSWORD" ]   && sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env

echo "Generating application key..."
php artisan key:generate --force --no-interaction

echo "Clearing config cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Running migrations..."
php artisan migrate --force

echo "Caching configs..."
php artisan cache:clear
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]