#!/bin/sh
set -e

echo "=== HMS Docker Container Starting ==="

# Handle Database Configuration for Render
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_HOST" ]; then
    echo "Configuring SQLite database..."
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/html/database/database.sqlite
    if [ ! -f "$DB_DATABASE" ]; then
        touch "$DB_DATABASE"
        echo "Created SQLite database file at $DB_DATABASE"
    fi
fi

# Ensure storage directories exist and are writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache database
chmod -R 777 storage database

# Ensure valid APP_KEY exists before caching config
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "Generating base64 application key..."
    php artisan key:generate --force
fi

# Run migrations automatically
echo "Executing database migrations..."
php artisan migrate --force

# Seed initial data (roles, demo users, rooms, bookings)
echo "Seeding initial database records..."
php artisan db:seed --force || echo "Seeding completed or already seeded"

# Cache configuration, routes, and views for optimal performance
echo "Caching Laravel application configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Determine port (Render sets $PORT env var dynamically)
PORT=${PORT:-8000}
echo "HMS application ready! Starting server on port $PORT..."

exec php artisan serve --host=0.0.0.0 --port=$PORT
