#!/bin/sh
set -e

echo "=== HMS Docker Container Starting ==="

# Create database directory & SQLite file if needed
mkdir -p /var/www/html/database /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache /var/www/html/storage/logs
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    echo "Created SQLite database file at /var/www/html/database/database.sqlite"
fi

# Ensure writable permissions
chmod -R 777 /var/www/html/storage /var/www/html/database /var/www/html/database/database.sqlite

# Create .env from .env.example if missing
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Clear any stale config cache first
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Validate and force a proper base64 APP_KEY
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    echo "Generating fresh base64 application key..."
    NEW_KEY=$(php artisan key:generate --show --no-interaction)
    export APP_KEY="$NEW_KEY"
    echo "Exported valid APP_KEY to container environment."
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
