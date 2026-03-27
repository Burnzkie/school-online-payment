#!/bin/bash

echo "🚀 Starting Laravel build on Render..."

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "✅ Composer dependencies installed"

# Clear and cache configs (safe on Render)
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ Laravel optimizations completed"