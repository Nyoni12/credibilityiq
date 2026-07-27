#!/usr/bin/env bash
# Run this once after uploading files to cPanel (via SSH or the cPanel Terminal).
# Usage: bash post-deploy.sh

set -e
cd "$(dirname "$0")"

composer audit
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:clear
php artisan storage:link 2>/dev/null || true

echo "Deploy complete."
