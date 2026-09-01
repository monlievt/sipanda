#!/bin/bash
set -e

echo "🚀 Memulai Deployment Produksi SIPANDA Web..."

# 1. Maintenance Mode
php artisan down || true

# 2. Pull Kode Terbaru
# git pull origin main

# 3. Install Dependensi PHP (Optimized)
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Database Migrations
php artisan migrate --force

# 5. Clear & Rebuild Caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart Queue Worker
php artisan queue:restart || true

# 7. Keluar dari Maintenance Mode
php artisan up

echo "✅ SIPANDA Web Berhasil Dideploy ke Produksi!"
