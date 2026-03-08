# Use official PHP with Apache (recommended for Laravel on Render)
# Pick the version matching your app (e.g., 8.2, 8.3 — check composer.json "php" requirement)
FROM php:8.3-apache

# Install system dependencies + common PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        gd \
        exif \
        pcntl \
        bcmath \
    && a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Set proper permissions (Laravel needs storage & bootstrap/cache writable)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Install Composer dependencies (production mode)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Apache to point to Laravel's public/ folder
# (most important change — Apache serves from /public by default now)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Optional: copy custom php.ini if you have one
# COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Expose port 80 (Render expects this)
EXPOSE 80

# Start Apache in foreground (Render runs this)
CMD ["apache2-foreground"]
