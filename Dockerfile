FROM dunglas/frankenphp:1.13.3-php8.5.12-trixie

WORKDIR /app

COPY --from=composer:2.10.3 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_mysql zip

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress

COPY . .
