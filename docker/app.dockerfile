FROM php:8.2-alpine

#https://github.com/api-platform/api-platform/issues/2339
RUN apk add --update linux-headers

RUN mv /usr/local/etc/php/php.ini-development "$PHP_INI_DIR/php.ini"

RUN sed -i "s/memory_limit = 128M/memory_limit = 1024M/g" /usr/local/etc/php/php.ini

RUN docker-php-ext-install pdo pdo_mysql

#Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

EXPOSE 8000
