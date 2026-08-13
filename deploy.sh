#!/bin/bash
#
# Wdrożenie aplikacji na serwer produkcyjny.
# Repozytorium jest sklonowane bezpośrednio do PROJECT_DIR - artisan, public/
# i storage/ leżą w jego korzeniu.
#
# Użycie: sudo ./deploy.sh
#
set -euo pipefail

PROJECT_DIR="${PROJECT_DIR:-/var/www/projekciarz.pl}"
WEB_USER="${WEB_USER:-www-data}"
PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.2-fpm}"

run_as_web() {
    sudo -u "$WEB_USER" "$@"
}

if [ ! -f "$PROJECT_DIR/artisan" ]; then
    echo "BŁĄD: nie znaleziono $PROJECT_DIR/artisan - sprawdź PROJECT_DIR." >&2
    exit 1
fi

cd "$PROJECT_DIR"
echo "Wdrożenie: $PROJECT_DIR (użytkownik: $WEB_USER)"

echo "[1/9] Tryb konserwacji..."
run_as_web php artisan down --retry=60 || true
# Zdejmij tryb konserwacji nawet jeśli dalsza część skryptu padnie.
trap 'cd "$PROJECT_DIR" && run_as_web php artisan up || true' EXIT

echo "[2/9] Pobieranie zmian z Git..."
run_as_web git pull origin main

echo "[3/9] Zależności PHP..."
run_as_web composer install --no-dev --optimize-autoloader --no-interaction

echo "[4/9] Build frontendu..."
run_as_web npm ci
run_as_web npm run build

echo "[5/9] Migracje..."
run_as_web php artisan migrate --force

echo "[6/9] Link do storage..."
run_as_web php artisan storage:link || true

echo "[7/9] Uprawnienia..."
# Katalogi 755, pliki 644 - NIE chmod -R 755 na wszystkim, bo to nadaje
# bit wykonywalny plikom i otwiera .env na odczyt dla całego systemu.
chown -R "$WEB_USER:$WEB_USER" "$PROJECT_DIR"
find "$PROJECT_DIR" -type d -not -path "*/node_modules/*" -exec chmod 755 {} \;
find "$PROJECT_DIR" -type f -not -path "*/node_modules/*" -exec chmod 644 {} \;
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
chmod 750 "$PROJECT_DIR/deploy.sh"

# Sekrety wyłącznie dla właściciela.
for secret in "$PROJECT_DIR/.env" "$PROJECT_DIR/.env.production"; do
    [ -f "$secret" ] && chmod 600 "$secret"
done

echo "[8/9] Cache konfiguracji..."
run_as_web php artisan optimize:clear
run_as_web php artisan config:cache
run_as_web php artisan route:cache
run_as_web php artisan view:cache
run_as_web php artisan event:cache

echo "[9/9] Restart usług..."
systemctl restart "$PHP_FPM_SERVICE" || systemctl restart php-fpm
# Worker kolejki musi wystartować na nowym kodzie.
run_as_web php artisan queue:restart || true

echo
echo "Audyt bezpieczeństwa:"
run_as_web php artisan security:audit || true

echo
echo "Gotowe."
