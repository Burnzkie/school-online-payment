#!/usr/bin/env bash

echo "Running composer..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "Generating app key if needed..."
php artisan key:generate --show

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

# If you have frontend assets (Vite, npm, etc.)
# echo "Installing npm dependencies..."
# npm install
# echo "Building assets..."
# npm run build

echo "Running migrations (if any)..."
php artisan migrate --force --no-interaction

# Optional: storage link
php artisan storage:link

echo "Deployment prep complete!"
