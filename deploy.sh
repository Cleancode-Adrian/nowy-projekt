#!/bin/bash

# Skrypt deploymentu dla Projekciarz.pl
# Użycie: ./deploy.sh

set -e

echo "🚀 Rozpoczynam deployment..."

# Katalog projektu (dostosuj do swojej ścieżki)
PROJECT_DIR="/var/www/projekciarz.pl"
cd $PROJECT_DIR

# 1. Pobierz najnowsze zmiany z gita
echo "📥 Pobieram zmiany z gita..."
sudo -u www-data git pull origin main

# 2. Zainstaluj zależności Composer (jeśli są nowe)
echo "📦 Aktualizuję zależności Composer..."
sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction

# 3. Zainstaluj zależności NPM i zbuduj assety (jeśli są zmiany w frontendzie)
if [ -f "package.json" ]; then
    echo "🎨 Buduję assety frontend..."
    sudo -u www-data npm install --production
    sudo -u www-data npm run build
fi

# 4. Uruchom migracje (jeśli są nowe)
echo "🗄️ Sprawdzam migracje..."
sudo -u www-data php artisan migrate --force

# 5. Wyczyść cache
echo "🧹 Czyszczę cache..."
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear

# 6. Zoptymalizuj dla produkcji
echo "⚡ Optymalizuję dla produkcji..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 7. Restart PHP-FPM (opcjonalnie, jeśli potrzebne)
# sudo systemctl restart php8.2-fpm

echo "✅ Deployment zakończony pomyślnie!"
echo "🌐 Sprawdź stronę: https://projekciarz.pl"

