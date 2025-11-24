# 🚀 Instrukcja wdrożenia na serwer

## Szybka aktualizacja (jeśli projekt już jest na serwerze)

### Opcja 1: Użyj skryptu deploy.sh

1. Prześlij plik `deploy.sh` na serwer (lub stwórz go na serwerze)
2. Nadaj uprawnienia do wykonania:
```bash
chmod +x deploy.sh
```

3. Uruchom skrypt:
```bash
./deploy.sh
```

### Opcja 2: Ręczne wykonanie komend

Połącz się z serwerem przez SSH i wykonaj:

```bash
# Przejdź do katalogu projektu
cd /var/www/projekciarz.pl
# (lub cd /var/www/html - zależnie od konfiguracji)

# Napraw uprawnienia (jeśli potrzebne)
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo chmod -R 755 /var/www/projekciarz.pl
sudo chmod -R 775 /var/www/projekciarz.pl/storage
sudo chmod -R 775 /var/www/projekciarz.pl/bootstrap/cache

# Pobierz najnowsze zmiany (jako www-data)
sudo -u www-data git pull origin main

# Zaktualizuj zależności PHP (jako www-data)
sudo -u www-data composer install --no-dev --optimize-autoloader

# Zaktualizuj zależności Node.js i zbuduj assety (jako www-data)
sudo -u www-data npm install
sudo -u www-data npm run build

# Uruchom migracje (jako www-data)
sudo -u www-data php artisan migrate --force

# Zoptymalizuj cache (jako www-data)
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
# (lub: sudo systemctl restart php-fpm)
```

---

## Pierwsze wdrożenie (jeśli projekt nie jest jeszcze na serwerze)

### 1. Połącz się z serwerem przez SSH

```bash
ssh uzytkownik@twoj-serwer.pl
```

### 2. Sklonuj repozytorium

```bash
cd /var/www
sudo git clone https://github.com/Cleancode-Adrian/nowy-projekt.git nazwa-projektu
cd nazwa-projektu
```

### 3. Zainstaluj zależności

```bash
# PHP
composer install --no-dev --optimize-autoloader

# Node.js
npm install
npm run build
```

### 4. Skonfiguruj uprawnienia

```bash
sudo chown -R www-data:www-data /var/www/nazwa-projektu
sudo chmod -R 755 /var/www/nazwa-projektu
sudo chmod -R 775 /var/www/nazwa-projektu/storage
sudo chmod -R 775 /var/www/nazwa-projektu/bootstrap/cache
```

### 5. Skonfiguruj plik .env

```bash
cp .env.example .env
nano .env  # Edytuj konfigurację
```

**Ważne ustawienia w .env:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://twoja-domena.pl`
- `DB_*` - dane dostępowe do bazy danych produkcyjnej
- `MAIL_*` - konfiguracja poczty

### 6. Wygeneruj klucz i uruchom migracje

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### 7. Zoptymalizuj dla produkcji

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Skonfiguruj Nginx

Utwórz plik `/etc/nginx/sites-available/nazwa-projektu`:

```nginx
server {
    listen 80;
    server_name twoja-domena.pl;
    root /var/www/nazwa-projektu/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktywuj konfigurację:

```bash
sudo ln -s /etc/nginx/sites-available/nazwa-projektu /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 9. Skonfiguruj SSL (opcjonalnie)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d twoja-domena.pl
```

---

## 🔄 Automatyczna aktualizacja przez Cron (opcjonalnie)

Możesz skonfigurować automatyczne aktualizacje:

```bash
crontab -e
```

Dodaj linię (aktualizacja codziennie o 3:00):

```bash
0 3 * * * cd /var/www/nazwa-projektu && git pull origin main && composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## ⚠️ Ważne uwagi

1. **Zawsze rób backup przed aktualizacją:**
```bash
cp -r /var/www/nazwa-projektu /var/www/nazwa-projektu-backup-$(date +%Y%m%d)
```

2. **Sprawdź logi po aktualizacji:**
```bash
tail -f /var/www/nazwa-projektu/storage/logs/laravel.log
```

3. **Jeśli coś pójdzie nie tak, przywróć backup:**
```bash
rm -rf /var/www/nazwa-projektu
mv /var/www/nazwa-projektu-backup-YYYYMMDD /var/www/nazwa-projektu
```

---

## 📞 Wsparcie

W razie problemów sprawdź:
- Logi Laravel: `storage/logs/laravel.log`
- Logi Nginx: `/var/log/nginx/error.log`
- Logi PHP-FPM: `/var/log/php8.2-fpm.log`

