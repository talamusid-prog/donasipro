#!/bin/bash

echo "🚀 Starting deployment process..."

# Install/update dependencies
echo "📦 Installing dependencies..."
npm install

# Build assets for production
echo "🔨 Building assets for production..."
npm run build

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 public/build
chown -R www-data:www-data public/build

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed successfully!"
echo "🌐 Your application should now work without Vite errors."
