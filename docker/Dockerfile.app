FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    libicu-dev default-mysql-client \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring zip intl bcmath opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY backend/ .
RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

COPY docker/entrypoint-app.sh /usr/local/bin/entrypoint-app.sh
RUN chmod +x /usr/local/bin/entrypoint-app.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint-app.sh"]
CMD ["php-fpm"]
