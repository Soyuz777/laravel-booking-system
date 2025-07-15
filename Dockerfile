FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip sqlite3 libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy default .env file
RUN cp .env.example .env

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate key AFTER .env is present
RUN php artisan config:clear && php artisan key:generate

# Create SQLite file
RUN touch /tmp/database.sqlite

# Run migrations (optional: comment this out for first deploy)
RUN php artisan migrate --force

# Cache config
RUN php artisan config:cache

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=10000
