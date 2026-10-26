FROM dunglas/frankenphp:php8.5-bookworm

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_mysql zip

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress

COPY . .
