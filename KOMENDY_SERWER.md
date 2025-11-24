# 🚀 Komendy do wykonania na serwerze produkcyjnym

## Po zalogowaniu się przez SSH:

```bash
# 1. Przejdź do katalogu projektu
cd /var/www/projekciarz.pl

# 2. Pobierz najnowsze zmiany z Git
sudo -u www-data git pull origin main

# 3. Uruchom migracje (dodaje category_id, featured_image_alt do blog_posts oraz type do tags)
sudo -u www-data php artisan migrate --force

# 4. Wyczyść i zoptymalizuj cache
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear

# 5. Zoptymalizuj cache dla produkcji
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# 6. (Opcjonalnie) Zaktualizuj zależności jeśli były zmiany
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build

# 7. Restart PHP-FPM
sudo systemctl restart php8.2-fpm
# lub jeśli używasz innej wersji:
# sudo systemctl restart php-fpm
```

## ⚠️ Ważne uwagi:

1. **Migracje** - dodają:
   - `category_id` i `featured_image_alt` do tabeli `blog_posts`
   - `type` (announcement/blog) do tabeli `tags`

2. **Po migracji** - istniejące tagi będą miały `type = 'announcement'`
   - Tagi dla blogów będą tworzone automatycznie przy dodawaniu wpisów

3. **Status "zamknięte"** - zamknięte ogłoszenia są teraz widoczne (nie znikają)

4. **Nowe funkcje**:
   - Możliwość dodawania nowych tagów bezpośrednio z formularza bloga
   - Możliwość dodawania nowych kategorii bezpośrednio z formularza bloga
   - Tagi i kategorie są teraz w osobnych sekcjach

## ✅ Sprawdzenie po aktualizacji:

```bash
# Sprawdź czy migracje się wykonały
sudo -u www-data php artisan migrate:status

# Sprawdź logi jeśli coś nie działa
tail -f storage/logs/laravel.log
```

