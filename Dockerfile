FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1   # Logs PHP errors to stderr (visible in Render logs)
ENV RUN_SCRIPTS 1         # Ensures /scripts/*.sh run if needed (backup to cont-init.d)
ENV REAL_IP_HEADER 1      # Handles real client IPs behind Render's proxy

# Laravel-specific
ENV APP_ENV production    # Sets Laravel to production mode
ENV APP_DEBUG false       # Disables debug mode for security
ENV LOG_CHANNEL stderr    # Logs to stderr (Render console)
ENV PHP_OPCACHE_ENABLE 1
ENV COMPOSER_ALLOW_SUPERUSER 1  # Allows Composer to run as root in container

# Add any custom PHP config if needed (optional)
# COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy deploy script
COPY scripts/00-laravel-deploy.sh /etc/cont-init.d/00-laravel-deploy.sh

RUN chmod +x /etc/cont-init.d/00-laravel-deploy.sh

CMD ["/start.sh"]
