FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip sqlite3 libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files
COPY . .

# Copy environment file
RUN cp .env.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key and cache config
RUN php artisan key:generate
RUN php artisan config:clear
RUN php artisan config:cache

# Create SQLite database file
RUN touch /tmp/database.sqlite

# ⚠️ Skip migration here to avoid build-time failure
# Laravel will run migration automatically via AppServiceProvider on first request

# Start Laravel app
CMD php artisan serve --host=0.0.0.0 --port=10000
