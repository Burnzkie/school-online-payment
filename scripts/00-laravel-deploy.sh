#!/usr/bin/env bash
set -e  # Exit immediately if any command fails (good for debugging)

echo "Changing to Laravel project directory..."
cd /var/www/html || { echo "Error: /var/www/html does not exist or is inaccessible"; exit 1; }

# Verify we're in the right place (optional but very helpful in logs)
if [ ! -f "composer.json" ] || [ ! -f "artisan" ]; then
  echo "Error: composer.json or artisan not found in /var/www/html"
  echo "→ Make sure your Root Directory on Render points to the folder containing composer.json"
  ls -la
  exit 1
fi

echo "Running composer install..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Generating app key if needed..."
# Use --force to avoid interactive prompt; --no-interaction for non-TTY environments
php artisan key:generate --force --no-interaction || true  # || true = don't fail if key already exists

echo "Caching config..."
php artisan config:cache --no-interaction

echo "Caching routes..."
php artisan route:cache --no-interaction

# Uncomment if you have frontend assets (Vite / Laravel Mix / npm)
# echo "Installing npm dependencies..."
# npm ci --no-audit --no-fund  # or npm install
# echo "Building assets..."
# npm run build  # or npm run prod / npm run render if custom

echo "Running migrations (if any)..."
php artisan migrate --force --no-interaction || true  # || true = continue even if no migrations or DB not ready yet

# Optional: create storage symlink (very common for public files)
echo "Creating storage symlink..."
php artisan storage:link --no-interaction || true

echo "Deployment prep complete!"
