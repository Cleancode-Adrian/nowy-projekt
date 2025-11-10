# Aplikacja Laravel - Backend

Aplikacja Laravel z Livewire do zarządzania ogłoszeniami, propozycjami, wiadomościami i portfolio.

## 📋 Wymagania

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL/MariaDB
- Git

## 🚀 Instalacja (Lokalna)

```bash
# Sklonuj repozytorium
git clone <URL_TWOJEGO_REPO>
cd backend

# Zainstaluj zależności PHP
composer install

# Zainstaluj zależności Node.js
npm install

# Skopiuj plik konfiguracyjny
cp .env.example .env

# Wygeneruj klucz aplikacji
php artisan key:generate

# Skonfiguruj bazę danych w pliku .env, następnie:
php artisan migrate --seed

# Zbuduj assety
npm run build

# Utwórz linki symboliczne dla storage
php artisan storage:link

# Uruchom serwer deweloperski
php artisan serve
```

## 🔧 Konfiguracja

1. Edytuj plik `.env` i ustaw:
   - `APP_NAME` - nazwa Twojej aplikacji
   - `APP_URL` - URL produkcyjny
   - `DB_*` - dane dostępowe do bazy danych
   - `MAIL_*` - konfiguracja poczty

2. Wygeneruj klucz aplikacji:
```bash
php artisan key:generate
```

## 📦 Wdrożenie na VPS

### Wymagania na serwerze:
- Ubuntu 22.04 / Debian 12 (lub nowszy)
- Nginx / Apache
- PHP 8.2+ z rozszerzeniami: mbstring, xml, bcmath, pdo, mysql, zip, gd
- MySQL/MariaDB
- Composer
- Node.js

### Kroki wdrożenia:

```bash
# 1. Sklonuj repozytorium na serwer
cd /var/www
git clone <URL_TWOJEGO_REPO> nazwa-projektu
cd nazwa-projektu

# 2. Zainstaluj zależności
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 3. Skonfiguruj uprawnienia
sudo chown -R www-data:www-data /var/www/nazwa-projektu
sudo chmod -R 755 /var/www/nazwa-projektu
sudo chmod -R 775 /var/www/nazwa-projektu/storage
sudo chmod -R 775 /var/www/nazwa-projektu/bootstrap/cache

# 4. Skonfiguruj .env
cp .env.example .env
nano .env  # Edytuj konfigurację

# 5. Wygeneruj klucz i uruchom migracje
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# 6. Optymalizacja dla produkcji
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Konfiguracja Nginx

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

### SSL (Certbot)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d twoja-domena.pl
```

## 🔄 Aktualizacja na serwerze

```bash
cd /var/www/nazwa-projektu
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.2-fpm
```

## 📝 Licencja

[Dodaj swoją licencję]

