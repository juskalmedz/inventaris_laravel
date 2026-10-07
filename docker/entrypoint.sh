#!/bin/sh
set -e

# Copy .env.example to .env if .env doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Ensure storage directories and permissions
mkdir -p /var/www/html/storage/app/public \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate application key if not set
if grep -q "APP_KEY=base64:GENERATE_WITH_ARTISAN_KEY_GENERATE=" /var/www/html/.env || grep -q "APP_KEY=$" /var/www/html/.env; then
    echo "Generating Laravel Application Key..."
    php artisan key:generate --force || true
fi

# Wait for database if DB_HOST is set and not localhost
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "Waiting for database connection at $DB_HOST:$DB_PORT..."
    max_tries=30
    count=0
    until nc -z -v -w3 "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null || [ $count -gt $max_tries ]; do
        echo "Waiting for database ($count/$max_tries)..."
        sleep 2
        count=$((count + 1))
    done
    if [ $count -le $max_tries ]; then
        echo "Database is ready! Running migrations and seeders..."
        php artisan migrate --force || true
        php artisan db:seed --force || true
    fi
fi

# Execute the main container command (php-fpm)
exec "$@"
