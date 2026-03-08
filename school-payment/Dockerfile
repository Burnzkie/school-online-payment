# Use official PHP 8.3 with Apache – matches your composer.json ^8.2 requirement
FROM php:8.3-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies + libraries needed for PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev      # for mbstring \
    libxml2-dev      # for xml/dom \
    libicu-dev       # for intl \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
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

# Copy application code (do this early so we can cache composer install)
COPY . .

# Set Laravel/Filament production environment variables
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PHP_OPCACHE_ENABLE=1 \
    COMPOSER_ALLOW_SUPERUSER=1

# Install Composer from official image (multi-stage for smaller final image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies (production mode – no dev packages)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set proper ownership & permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Configure Apache to serve from Laravel's /public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Optional: If you have a custom php.ini, uncomment and adjust path
# COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Expose port 80 (Render expects this)
EXPOSE 80

# Start Apache in foreground (default for php:*-apache images)
CMD ["apache2-foreground"]
