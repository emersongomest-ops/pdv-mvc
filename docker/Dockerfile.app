# syntax=docker/dockerfile:1.7

# Digests: docker/images.lock (bash scripts/docker-pin-digests.sh)
ARG PHP_IMAGE=php:8.3-fpm-bookworm@sha256:2a397791f5ee422190bb673d79332be53ff545205f6df19e2664bd664ebbd739
ARG COMPOSER_IMAGE=composer:2@sha256:5946476338742b200bb9ff88f8be56275ddae4b3949c72305cb0dbf10cfcb760

FROM ${COMPOSER_IMAGE} AS composer

FROM ${PHP_IMAGE} AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
        libicu-dev default-mysql-client gosu procps \
    && docker-php-ext-install \
        pdo_mysql mbstring zip intl bcmath opcache pcntl \
    && printf '\n\n\n\n\n\n' | pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

FROM base AS vendor

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY backend/composer.json backend/composer.lock ./
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

FROM base AS app

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor

COPY backend/ .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache storage/app \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && rm -f /usr/bin/composer

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/entrypoint-app.sh /usr/local/bin/entrypoint-app.sh
RUN chmod +x /usr/local/bin/entrypoint-app.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint-app.sh"]
CMD ["php-fpm"]
