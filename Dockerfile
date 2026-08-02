FROM php:8.2-cli-alpine

# Install helper for fast PHP extension installations
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system dependencies and required PHP extensions
RUN apk add --no-cache git curl nodejs npm sqlite \
    && install-php-extensions pdo_mysql pdo_sqlite gd zip intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies for production
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies and build production assets with Vite
RUN npm ci && npm run build && rm -rf node_modules

# Set writable permissions for storage and database
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache database \
    && chmod -R 777 storage database

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
