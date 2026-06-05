FROM php:8.3-fpm-alpine

RUN apk update && apk add --no-cache \
    postgresql-dev \
    libpq \
    git \
    curl \
    zip \
    unzip \
    npm \
    nodejs

RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/backend

COPY composer.json composer.lock ./

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-interaction --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www/backend && chmod -R 755 /var/www/backend/storage

EXPOSE 8000

CMD ["php-fpm"]
