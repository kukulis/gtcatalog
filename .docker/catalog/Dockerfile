FROM php:7.4.33-fpm

ARG SYSTEM_USER
ARG SYSTEM_UID
ARG SYSTEM_GID

WORKDIR /var/www/html

RUN apt-get update
RUN apt-get -y install less
RUN apt-get -y install vim
RUN apt-get -y install iputils-ping zip libzip-dev libpng-dev

RUN pecl install xdebug
#RUN pecl install redis
#RUN pecl install mongodb
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install zip gd
#RUN docker-php-ext-enable redis
#RUN docker-php-ext-enable mongodb

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer