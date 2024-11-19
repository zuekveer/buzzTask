FROM php:8.2-fpm-alpine

ARG UID

COPY ./conf.d /usr/local/etc/php/conf.d

RUN apk add --no-cache --update \
    $PHPIZE_DEPS \
    shadow \
    icu-dev \
    libzip-dev

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql && docker-php-ext-configure intl --enable-intl

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) pdo_mysql intl zip opcache

# Create PHP configuration
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN usermod -u ${UID} www-data

# Set working directory
WORKDIR /var/www/test-task

USER ${UID}
