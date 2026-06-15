#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "Installing PHP dependencies..."
COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

echo "Installing Node dependencies..."
npm ci

echo "Building frontend assets for production..."
npm run build

echo "Caching Laravel configuration..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "Running database migrations (Supabase PostgreSQL)..."
php artisan migrate --force

echo "Build complete."
