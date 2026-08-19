#!/bin/bash
#
# Solva deploy script — run this on the SERVER (via SSH), from inside
# the project root (the "solva" folder, not public_html).
#
# Usage:
#   cd ~/domains/<your-domain>/solva
#   bash deploy.sh
#
# IMPORTANT: this does NOT build frontend assets. Vite/npm can't run on
# Hostinger's shared hosting — you must run `npm run build` on your LOCAL
# machine and commit the resulting public/build folder BEFORE running this
# script, or the site will throw a "Vite manifest not found" error.

set -e  # stop immediately if any command fails, instead of continuing blindly

echo "==> Pulling latest code from main..."
git pull origin main

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Clearing stale caches..."
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo "==> Rebuilding caches..."
php artisan config:cache
php artisan route:cache

echo "==> Done. Deploy complete."
