FROM php:8.2-cli-alpine

# Install system dependencies & required PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    icu-dev \
    sqlite-dev \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite gd zip mbstring bcmath intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies (production mode)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies & build Vite assets
RUN npm ci && npm run build && rm -rf node_modules

# Set permissions for storage & database
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache database \
    && chmod -R 777 storage database

# Entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
