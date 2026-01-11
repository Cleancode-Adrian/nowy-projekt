# 🔍 Diagnostyka błędu 500 - Szybki przewodnik

## ⚡ Szybkie rozwiązanie (krok po kroku)

### Krok 1: Sprawdź logi Laravel na serwerze

```bash
# Zaloguj się na serwer przez SSH
ssh uzytkownik@serwer.pl

# Przejdź do katalogu projektu
cd /var/www/projekciarz.pl

# Sprawdź ostatnie błędy w logach
sudo tail -n 100 storage/logs/laravel.log

# LUB sprawdź w czasie rzeczywistym
sudo tail -f storage/logs/laravel.log
```

### Krok 2: Wyczyść cache Laravel

```bash
cd /var/www/projekciarz.pl

# Wyczyść wszystkie cache
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear

# Zbuduj cache ponownie
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### Krok 3: Sprawdź uprawnienia

```bash
cd /var/www/projekciarz.pl

# Napraw uprawnienia
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo chmod -R 755 /var/www/projekciarz.pl
sudo chmod -R 775 /var/www/projekciarz.pl/storage
sudo chmod -R 775 /var/www/projekciarz.pl/bootstrap/cache
```

### Krok 4: Sprawdź składnię PHP

```bash
cd /var/www/projekciarz.pl

# Sprawdź składnię wszystkich plików PHP
find app -name "*.php" -exec php -l {} \;

# Sprawdź konkretny plik (jeśli wiesz który może być problematyczny)
php -l app/Http/Controllers/Auth/AuthController.php
```

### Krok 5: Restart PHP-FPM

```bash
sudo systemctl restart php8.4-fpm
# LUB
sudo systemctl restart php-fpm
```

---

## 🔎 Najczęstsze przyczyny błędu 500

### 1. Błąd składniowy w PHP
**Objawy:** Błąd 500, w logach: `Parse error: syntax error`

**Rozwiązanie:**
```bash
# Sprawdź składnię
php -l app/Http/Controllers/Auth/AuthController.php

# Jeśli jest błąd, popraw go i wdróż ponownie
```

### 2. Uszkodzony cache
**Objawy:** Błąd 500 po deploymencie

**Rozwiązanie:**
```bash
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear
```

### 3. Problemy z bazą danych
**Objawy:** Błąd 500, w logach: `SQLSTATE` lub `Connection refused`

**Rozwiązanie:**
```bash
# Sprawdź połączenie z bazą
sudo -u www-data php artisan tinker
>>> DB::connection()->getPdo();

# Sprawdź migracje
sudo -u www-data php artisan migrate:status
```

### 4. Brakujące pliki lub zależności
**Objawy:** Błąd 500, w logach: `Class not found` lub `File not found`

**Rozwiązanie:**
```bash
# Zainstaluj zależności ponownie
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data composer dump-autoload
```

### 5. Problemy z uprawnieniami
**Objawy:** Błąd 500, w logach: `Permission denied`

**Rozwiązanie:**
```bash
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo chmod -R 755 /var/www/projekciarz.pl
sudo chmod -R 775 /var/www/projekciarz.pl/storage
sudo chmod -R 775 /var/www/projekciarz.pl/bootstrap/cache
```

---

## 📋 Kompletna komenda diagnostyczna (wszystko w jednej linii)

```bash
cd /var/www/projekciarz.pl && \
sudo -u www-data php artisan config:clear && \
sudo -u www-data php artisan route:clear && \
sudo -u www-data php artisan view:clear && \
sudo -u www-data php artisan cache:clear && \
sudo -u www-data composer dump-autoload && \
sudo chown -R www-data:www-data /var/www/projekciarz.pl && \
sudo chmod -R 775 /var/www/projekciarz.pl/storage && \
sudo chmod -R 775 /var/www/projekciarz.pl/bootstrap/cache && \
sudo -u www-data php artisan config:cache && \
sudo -u www-data php artisan route:cache && \
sudo -u www-data php artisan view:cache && \
sudo systemctl restart php8.4-fpm
```

---

## 🔍 Sprawdzenie logów - szczegółowe

### Wyświetl ostatnie 50 linii z błędami:
```bash
sudo tail -n 50 storage/logs/laravel.log | grep -i error
```

### Wyświetl wszystkie błędy z ostatniej godziny:
```bash
sudo grep -i "error\|exception\|fatal" storage/logs/laravel.log | tail -n 50
```

### Wyświetl błędy z konkretnej daty:
```bash
sudo grep "2025-01-XX" storage/logs/laravel.log | grep -i error
```

### Sprawdź logi PHP-FPM:
```bash
sudo tail -n 50 /var/log/php8.4-fpm.log
# LUB
sudo tail -n 50 /var/log/php-fpm.log
```

### Sprawdź logi Nginx/Apache:
```bash
# Nginx
sudo tail -n 50 /var/log/nginx/error.log

# Apache
sudo tail -n 50 /var/log/apache2/error.log
```

---

## 🛠️ Włącz tryb debugowania (tymczasowo)

**UWAGA:** Włącz tylko na chwilę, żeby zobaczyć szczegóły błędu!

```bash
cd /var/www/projekciarz.pl

# Edytuj .env
sudo nano .env

# Zmień:
APP_DEBUG=true
APP_ENV=local

# Wyczyść cache
sudo -u www-data php artisan config:clear

# Sprawdź stronę - zobaczysz szczegóły błędu
# PAMIĘTAJ: Wyłącz debug po naprawie!
```

**Po naprawie wyłącz debug:**
```bash
# W .env zmień z powrotem:
APP_DEBUG=false
APP_ENV=production

sudo -u www-data php artisan config:cache
```

---

## 📞 Jeśli nadal nie działa

1. **Sprawdź logi** - skopiuj ostatnie 100 linii z `storage/logs/laravel.log`
2. **Sprawdź konfigurację** - czy `.env` jest poprawnie skonfigurowany
3. **Sprawdź wersję PHP** - `php -v` (powinna być 8.2+)
4. **Sprawdź rozszerzenia PHP** - `php -m` (powinny być: mbstring, xml, pdo, mysql, etc.)

---

## 🎯 Szybka naprawa po deploymencie

Jeśli błąd 500 pojawił się zaraz po deploymencie:

```bash
cd /var/www/projekciarz.pl

# 1. Wyczyść wszystko
sudo -u www-data php artisan optimize:clear

# 2. Zainstaluj zależności ponownie
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data composer dump-autoload

# 3. Zbuduj cache
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 4. Restart
sudo systemctl restart php8.4-fpm
```

---

## 💡 Najczęstsze błędy po naszych zmianach

### Błąd: "Class 'App\Mail\UserRegisteredMail' not found"
**Rozwiązanie:**
```bash
sudo -u www-data composer dump-autoload
```

### Błąd: "Call to undefined method"
**Rozwiązanie:**
```bash
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
```

### Błąd: "View not found"
**Rozwiązanie:**
```bash
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan view:cache
```


