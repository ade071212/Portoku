#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Running composer install..."
composer install --no-dev --optimize-autoloader

echo "Clearing caches..."
php artisan optimize:clear

echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link
