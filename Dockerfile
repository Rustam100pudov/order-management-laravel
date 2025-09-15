FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update \
    && apt-get install -y \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        libzip-dev \
        npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install npm dependencies and build assets
RUN npm install && npm run build || true

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 8000 and start Laravel server
EXPOSE 8000
CMD sh -c '\
    if [ "$DB_CONNECTION" = "sqlite" ] && [ -n "$DB_DATABASE" ] && [ ! -f "$DB_DATABASE" ]; then \
        mkdir -p $(dirname "$DB_DATABASE") && touch "$DB_DATABASE"; \
    fi && \
    php artisan storage:link || true && \
    php artisan migrate --force || true && \
    php artisan config:cache && php artisan route:cache && php artisan view:cache && \
    php -S 0.0.0.0:${PORT:-8000} -t public'
