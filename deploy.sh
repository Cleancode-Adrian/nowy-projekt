#!/bin/bash

# Skrypt do aktualizacji aplikacji na serwerze produkcyjnym
# Użycie: ./deploy.sh

set -e  # Zatrzymaj przy błędzie

echo "🚀 Rozpoczynam aktualizację aplikacji..."

# Przejdź do katalogu projektu (zmień ścieżkę na swoją)
PROJECT_DIR="/var/www/projekciarz.pl"
cd "$PROJECT_DIR" || exit 1

echo "📥 Pobieram najnowsze zmiany z Git..."
sudo -u www-data git pull origin main

echo "🔧 Naprawiam uprawnienia..."
sudo chown -R www-data:www-data "$PROJECT_DIR"
sudo chmod -R 755 "$PROJECT_DIR"
sudo chmod -R 775 "$PROJECT_DIR/storage"
sudo chmod -R 775 "$PROJECT_DIR/bootstrap/cache"

echo "📦 Aktualizuję zależności PHP..."
sudo -u www-data composer install --no-dev --optimize-autoloader

echo "📦 Aktualizuję zależności Node.js..."
sudo -u www-data npm install

echo "🏗️ Buduję assety produkcyjne..."
sudo -u www-data npm run build

echo "🗄️ Uruchamiam migracje bazy danych..."
sudo -u www-data php artisan migrate --force

echo "🔗 Sprawdzam link do storage..."
sudo -u www-data php artisan storage:link || true

echo "⚡ Zoptymalizuję cache..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo "🔄 Restartuję PHP-FPM..."
sudo systemctl restart php8.2-fpm || sudo systemctl restart php-fpm

echo "✅ Aktualizacja zakończona pomyślnie!"
