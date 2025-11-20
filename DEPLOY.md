# 🚀 Instrukcja Deploymentu - Projekciarz.pl

## Szybki deployment (używając skryptu)

### 1. Prześlij skrypt na serwer
```bash
# Z lokalnego komputera
scp deploy.sh ubuntu@twoj-serwer.pl:/var/www/projekciarz.pl/
```

### 2. Nadaj uprawnienia i uruchom
```bash
# Zaloguj się na serwer
ssh ubuntu@twoj-serwer.pl

# Przejdź do katalogu projektu
cd /var/www/projekciarz.pl

# Nadaj uprawnienia
chmod +x deploy.sh

# Uruchom deployment
./deploy.sh
```

---

## Deployment ręczny (krok po kroku)

### 1. Zaloguj się na serwer
```bash
ssh ubuntu@twoj-serwer.pl
```

### 2. Przejdź do katalogu projektu
```bash
cd /var/www/projekciarz.pl
```

### 3. Pobierz najnowsze zmiany z gita
```bash
sudo -u www-data git pull origin main
```

### 4. Zainstaluj zależności (jeśli są nowe)
```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### 5. Zbuduj assety frontend (jeśli zmieniałeś CSS/JS)
```bash
sudo -u www-data npm install
sudo -u www-data npm run build
```

### 6. Uruchom migracje (jeśli są nowe)
```bash
sudo -u www-data php artisan migrate --force
```

### 7. Wyczyść i zoptymalizuj cache
```bash
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear

# Zoptymalizuj dla produkcji
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 8. (Opcjonalnie) Restart PHP-FPM
```bash
sudo systemctl restart php8.2-fpm
```

---

## Dodawanie nowych wpisów blogowych

Jeśli dodałeś nowe wpisy blogowe do seeder'a, po deploymentcie uruchom:

```bash
cd /var/www/projekciarz.pl
sudo -u www-data php artisan db:seed --class=BlogPostSeeder
```

**Uwaga:** To doda nowe wpisy do bazy. Jeśli wpisy już istnieją, możesz dostać błąd duplikatów. W takim przypadku możesz:
- Usunąć stare wpisy z bazy
- Lub użyć `--force` jeśli chcesz nadpisać

---

## Szybkie komendy (kopiuj-wklej)

Jeśli często aktualizujesz stronę, możesz stworzyć alias w `.bashrc`:

```bash
# Dodaj do ~/.bashrc
alias deploy-projekciarz='cd /var/www/projekciarz.pl && sudo -u www-data git pull origin main && sudo -u www-data composer install --no-dev --optimize-autoloader && sudo -u www-data php artisan view:clear && sudo -u www-data php artisan cache:clear && sudo -u www-data php artisan config:cache && sudo -u www-data php artisan route:cache'
```

Potem wystarczy wpisać:
```bash
deploy-projekciarz
```

---

## Troubleshooting

### Problem: "Permission denied"
```bash
# Sprawdź uprawnienia
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo chmod -R 755 /var/www/projekciarz.pl
sudo chmod -R 775 /var/www/projekciarz.pl/storage
sudo chmod -R 775 /var/www/projekciarz.pl/bootstrap/cache
```

### Problem: "Git pull nie działa"
```bash
# Sprawdź czy masz dostęp do repozytorium
sudo -u www-data git status

# Jeśli trzeba, skonfiguruj git
sudo -u www-data git config user.name "Deploy"
sudo -u www-data git config user.email "deploy@projekciarz.pl"
```

### Problem: "Composer install nie działa"
```bash
# Sprawdź czy composer jest zainstalowany
composer --version

# Jeśli nie, zainstaluj:
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## Automatyczny deployment (GitHub Actions) - opcjonalnie

Możesz skonfigurować automatyczny deployment przez GitHub Actions. Wtedy każdy push do `main` automatycznie wdroży zmiany na serwer.

