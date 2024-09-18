FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y libpq-dev libzip-dev libcurl4-openssl-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql curl zip

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

ENTRYPOINT ["docker-php-entrypoint"]
WORKDIR /var/www/html
EXPOSE 9000
CMD ["php-fpm"]