FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
        libzip-dev \
        libonig-dev \
        libpq-dev \
        unzip \
        cron \
        zlib1g-dev \
        libpng-dev \
    && docker-php-ext-install \
        gd \
        pdo_mysql \
        mysqli \
        zip \
    && a2enmod rewrite

COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin