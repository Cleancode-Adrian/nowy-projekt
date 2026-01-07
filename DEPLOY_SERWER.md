# 🚀 Instrukcja wdrożenia zmian na serwer

## ⚠️ WAŻNE: Problem z uprawnieniami Git

Jeśli dostajesz błąd:
```
fatal: detected dubious ownership in repository at '/var/www/projekciarz.pl'
```

**Rozwiązanie 1 (zalecane):** Użyj właściwego użytkownika:
```bash
# Jeśli repozytorium należy do www-data:
sudo -u www-data git pull origin main

# LUB jeśli należy do innego użytkownika:
sudo -u nazwa_uzytkownika git pull origin main
```

**Rozwiązanie 2:** Napraw właściciela repozytorium:
```bash
# Sprawdź właściciela:
ls -la /var/www/projekciarz.pl/.git

# Zmień właściciela na www-data (lub innego użytkownika):
sudo chown -R www-data:www-data /var/www/projekciarz.pl

# Teraz możesz użyć:
sudo -u www-data git pull origin main
```

**Rozwiązanie 3:** Dodaj katalog do safe.directory (dla root):
```bash
# Jako root:
git config --global --add safe.directory /var/www/projekciarz.pl
git config --global --add safe.directory '*'

# LUB dla konkretnego użytkownika:
sudo -u www-data git config --global --add safe.directory /var/www/projekciarz.pl
```

---

## Metoda 1: Automatyczna (użycie skryptu deploy.sh)

### Krok 1: Zaloguj się na serwer przez SSH
```bash
ssh uzytkownik@serwer.pl
```

### Krok 2: Przejdź do katalogu projektu
```bash
cd /var/www/projekciarz.pl
# LUB jeśli masz inną ścieżkę:
cd /sciezka/do/projektu/backend
```

### Krok 3: Uruchom skrypt deploy
```bash
chmod +x deploy.sh
# Użyj właściwego użytkownika (www-data lub inny):
sudo -u www-data ./deploy.sh
# LUB jeśli skrypt już ma sudo w środku:
./deploy.sh
```

Skrypt automatycznie wykona:
- ✅ `git pull origin main` - pobierze zmiany z Git
- ✅ `composer install` - zaktualizuje zależności PHP
- ✅ `npm install && npm run build` - zbuduje assety
- ✅ `php artisan migrate` - uruchomi migracje
- ✅ `php artisan config:cache` - zoptymalizuje cache
- ✅ Restart PHP-FPM

---

## Metoda 2: Ręczna (krok po kroku)

### Krok 1: Zaloguj się na serwer przez SSH
```bash
ssh uzytkownik@serwer.pl
```

### Krok 2: Przejdź do katalogu projektu
```bash
cd /var/www/projekciarz.pl
# LUB jeśli masz inną ścieżkę:
cd /sciezka/do/projektu/backend
```

### Krok 3: Pobierz zmiany z Git
```bash
# WAŻNE: Użyj właściwego użytkownika (www-data lub właściciel repozytorium)

# Jeśli masz lokalne zmiany (np. w package-lock.json), najpierw je odrzuć:
sudo -u www-data git reset --hard HEAD

# Teraz pobierz zmiany:
sudo -u www-data git pull origin main

# LUB jeśli jesteś już zalogowany jako właściwy użytkownik:
git reset --hard HEAD  # jeśli są lokalne zmiany
git pull origin main
```

### Krok 4: Zaktualizuj zależności PHP
```bash
composer install --no-dev --optimize-autoloader
```

### Krok 5: Zaktualizuj zależności Node.js i zbuduj assety
```bash
npm install
npm run build
```

### Krok 6: Uruchom migracje (jeśli są nowe)
```bash
php artisan migrate --force
```

### Krok 7: Zoptymalizuj cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Krok 8: Restart PHP-FPM (jeśli potrzebne)
```bash
sudo systemctl restart php8.2-fpm
# LUB
sudo systemctl restart php-fpm
```

### Krok 9: Napraw uprawnienia (jeśli potrzebne)
```bash
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo chmod -R 755 /var/www/projekciarz.pl
sudo chmod -R 775 /var/www/projekciarz.pl/storage
sudo chmod -R 775 /var/www/projekciarz.pl/bootstrap/cache
```

---

## ⚡ Szybka komenda (wszystko w jednej linii)

**Jako root (z sudo -u www-data dla Git):**
```bash
cd /var/www/projekciarz.pl && sudo -u www-data git reset --hard HEAD && sudo -u www-data git pull origin main && sudo -u www-data composer install --no-dev --optimize-autoloader && sudo -u www-data npm install && sudo -u www-data npm run build && sudo -u www-data php artisan migrate --force && sudo -u www-data php artisan config:cache && sudo -u www-data php artisan route:cache && sudo -u www-data php artisan view:cache && sudo systemctl restart php8.2-fpm
```

**Uwaga:** `git reset --hard HEAD` odrzuci lokalne zmiany. Jeśli masz ważne lokalne zmiany, użyj `git stash` zamiast tego.

**Jako użytkownik www-data (bez sudo):**
```bash
cd /var/www/projekciarz.pl && git pull origin main && composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
# Restart PHP-FPM wymaga root:
sudo systemctl restart php8.2-fpm
```

---

## 🔍 Sprawdzenie czy wszystko działa

Po wdrożeniu sprawdź:

1. **Czy strona się ładuje:**
   ```bash
   curl -I https://projekciarz.pl
   ```

2. **Czy nie ma błędów w logach:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Czy cache jest zaktualizowany:**
   ```bash
   php artisan config:clear  # Jeśli coś nie działa
   php artisan cache:clear
   ```

---

## ⚠️ Ważne uwagi

- **Zawsze rób backup przed wdrożeniem:**
  ```bash
  # Backup bazy danych
  mysqldump -u DB_USER -p DB_NAME > backup_$(date +%Y%m%d_%H%M%S).sql
  ```

- **Sprawdź czy plik `.env` jest poprawnie skonfigurowany** (nie jest w Git)

- **Jeśli masz problemy z uprawnieniami**, użyj:
  ```bash
  sudo chown -R www-data:www-data /var/www/projekciarz.pl
  ```

- **Jeśli coś nie działa**, wyczyść cache:
  ```bash
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  php artisan cache:clear
  ```

---

## 📝 Przykładowa sesja SSH

```bash
# 1. Połącz się z serwerem
ssh uzytkownik@projekciarz.pl

# 2. Przejdź do projektu
cd /var/www/projekciarz.pl

# 3. Sprawdź status Git
git status

# 4. Pobierz zmiany
git pull origin main

# 5. Zaktualizuj zależności
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 6. Migracje i cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Gotowe! Sprawdź stronę w przeglądarce
```

---

## 🎯 Najczęstsze problemy

### Problem: "dubious ownership" przy git pull
**Błąd:**
```
fatal: detected dubious ownership in repository at '/var/www/projekciarz.pl'
```

**Rozwiązanie:**
```bash
# Opcja 1: Użyj właściwego użytkownika
sudo -u www-data git pull origin main

# Opcja 2: Napraw właściciela
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo -u www-data git pull origin main

# Opcja 3: Dodaj do safe.directory (dla root)
git config --global --add safe.directory /var/www/projekciarz.pl
git config --global --add safe.directory '*'
```

### Problem: "Your local changes would be overwritten by merge"
**Błąd:**
```
error: Your local changes to the following files would be overwritten by merge:
	package-lock.json
Please commit your changes or stash them before you merge.
```

**Rozwiązanie (wybierz jedną opcję):**

**Opcja 1: Odrzuć lokalne zmiany (zalecane dla package-lock.json)**
```bash
# package-lock.json jest generowany automatycznie, więc można go bezpiecznie odrzucić
sudo -u www-data git reset --hard HEAD
sudo -u www-data git pull origin main
# Następnie zregeneruj package-lock.json:
sudo -u www-data npm install
```

**Opcja 2: Stash lokalne zmiany**
```bash
# Zapisz lokalne zmiany tymczasowo
sudo -u www-data git stash
sudo -u www-data git pull origin main
# Jeśli chcesz przywrócić zmiany (zwykle niepotrzebne dla package-lock.json):
sudo -u www-data git stash pop
```

**Opcja 3: Wymuś nadpisanie (ostrożnie!)**
```bash
# Wymuś pobranie zmian (nadpisze lokalne zmiany)
sudo -u www-data git fetch origin
sudo -u www-data git reset --hard origin/main
```

**Najlepsze rozwiązanie dla package-lock.json:**
```bash
# package-lock.json jest generowany przez npm, więc odrzuć go i zregeneruj:
cd /var/www/projekciarz.pl
sudo -u www-data git reset --hard HEAD
sudo -u www-data git pull origin main
sudo -u www-data npm install  # To wygeneruje nowy package-lock.json
```

### Problem: "Permission denied"
**Rozwiązanie:**
```bash
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo chmod -R 755 /var/www/projekciarz.pl
```

### Problem: "Composer not found"
**Rozwiązanie:**
```bash
# Zainstaluj composer globalnie lub użyj lokalnej wersji
php composer.phar install --no-dev --optimize-autoloader
```

### Problem: "npm not found"
**Rozwiązanie:**
```bash
# Zainstaluj Node.js i npm na serwerze
# LUB użyj npx
npx npm install && npx npm run build
```

### Problem: "Migration failed"
**Rozwiązanie:**
```bash
# Sprawdź logi
php artisan migrate --force --pretend
# Jeśli wszystko OK, uruchom bez --pretend
php artisan migrate --force
```

