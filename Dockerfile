FROM richarvey/nginx-php-fpm:3.1.6

COPY . .


ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1

ENV REAL_IP_HEADER 1


ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV PHP_OPCACHE_ENABLE 1
ENV COMPOSER_ALLOW_SUPERUSER 1




COPY scripts/00-laravel-deploy.sh /etc/cont-init.d/00-laravel-deploy.sh

RUN chmod +x /etc/cont-init.d/00-laravel-deploy.sh

CMD ["/start.sh"]
