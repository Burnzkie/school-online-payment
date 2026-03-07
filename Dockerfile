FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config from base image
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel production settings
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV PHP_OPCACHE_ENABLE 1
ENV COMPOSER_ALLOW_SUPERUSER 1

# Optional: custom PHP config (uncomment if needed)
# COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy deploy script and make it executable
COPY scripts/00-laravel-deploy.sh /etc/cont-init.d/00-laravel-deploy.sh

RUN chmod +x /etc/cont-init.d/00-laravel-deploy.sh

CMD ["/start.sh"]
