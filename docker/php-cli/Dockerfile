FROM php:8.2-cli-alpine

ARG UID

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

# Get Composer
COPY --from=composer:2.1 /usr/bin/composer /usr/local/bin/composer

RUN usermod -u ${UID} www-data

# Set working directory
WORKDIR /var/www/test-task

USER ${UID}
