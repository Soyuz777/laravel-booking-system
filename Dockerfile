FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip zip sqlite3 libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN php artisan key:generate
RUN touch /tmp/database.sqlite
RUN php artisan migrate --force
RUN php artisan config:cache

CMD php artisan serve --host=0.0.0.0 --port=10000
