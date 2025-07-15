FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip sqlite3 libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel files
COPY . .

# Copy .env
RUN cp .env.example .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate key
RUN php artisan key:generate

# Create empty SQLite DB
RUN touch /tmp/database.sqlite

# Cache config (skip migration)
RUN php artisan config:cache

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=10000
