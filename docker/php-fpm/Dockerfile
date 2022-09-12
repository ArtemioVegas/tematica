
FROM php:8.1-fpm

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install ctype \
    && docker-php-ext-install pdo_pgsql pgsql\
    && docker-php-ext-enable pdo_pgsql

#-e: It is used to exit immediately if a command exits with a non-zero status.
#-u: It is used to treat unset variables as an error when substituting.
#-x: It is used to print commands and their arguments in a sequential way (as they are executed).
RUN set -eux; apt-get update; apt-get install -y libzip-dev zlib1g-dev; docker-php-ext-install zip

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /app