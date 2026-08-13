# Bezpieczeństwo i wdrożenie — Projekciarz.pl

Dokument opisuje, co zostało naprawione w kodzie i co **trzeba zrobić ręcznie na serwerze**.
Kod sam z siebie nie naprawi konfiguracji nginx ani kont istniejących w bazie produkcyjnej.

---

## 1. Kolejność wdrożenia

Wykonuj po kolei. Krok 2 i 3 są ważniejsze od samego wgrania kodu.

### Krok 1 — sprawdź, gdzie wskazuje webroot

To jest najważniejsza pojedyncza rzecz.

```bash
grep -R "root " /etc/nginx/sites-enabled/
```

Musi być `/var/www/projekciarz.pl/public`.

Jeśli jest `/var/www/projekciarz.pl` — **plik `.env` z hasłami, katalog `.git`
i baza danych są publicznie do pobrania**. Sprawdź to od razu z zewnątrz:

```bash
curl -sI https://projekciarz.pl/.env
curl -sI https://projekciarz.pl/.git/config
curl -sI https://projekciarz.pl/ogloszenia_db
```

Każde z tych żądań ma zwrócić `404`. Jeśli którekolwiek zwraca `200` — sekrety wyciekły
i trzeba je zrotować (krok 3).

Gotowa konfiguracja: [deploy/nginx/projekciarz.pl.conf](deploy/nginx/projekciarz.pl.conf)

```bash
sudo cp deploy/nginx/projekciarz.pl.conf /etc/nginx/sites-available/projekciarz.pl
sudo ln -sf /etc/nginx/sites-available/projekciarz.pl /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### Krok 2 — posprzątaj pliki na serwerze

```bash
cd /var/www/projekciarz.pl
ls -la .env* ogloszenia_db 2>/dev/null

# Zrzut bazy i stare kopie .env nie mają czego szukać na produkcji:
sudo rm -f .env.backup .env.temp .env.przed-porzadkami ogloszenia_db

# .env tylko dla właściciela:
sudo chmod 600 .env
sudo chown www-data:www-data .env
```

### Krok 2b — baza danych w repozytorium GitHub

Plik `ogloszenia_db` (SQLite, **9 kont użytkowników z adresami e-mail i hashami
haseł** oraz 5 ogłoszeń) był śledzony przez gita i trafił do repozytorium
`nowy-projekt` w **6 commitach**, najstarszy to `5b3a500` (initial commit).

Zostało to naprawione w kodzie (`git rm --cached` + wpis w `.gitignore`), ale
**plik nadal jest w historii repozytorium** — usunięcie go z bieżącej wersji
nie usuwa go z commitów wstecz.

Do zrobienia ręcznie:

1. **Sprawdź widoczność repozytorium** na GitHubie
   (`Settings → General → Danger Zone`). Jeśli `nowy-projekt` jest **publiczne**,
   dane 9 osób były publicznie dostępne — to incydent podlegający RODO.
   Ustaw repozytorium na prywatne od razu.

2. **Wyczyść historię** (wymaga wymuszonego pusha, uprzedź współpracowników):

   ```bash
   pip install git-filter-repo
   git clone --mirror https://github.com/Cleancode-Adrian/nowy-projekt.git
   cd nowy-projekt.git
   git filter-repo --path ogloszenia_db --invert-paths
   git push --force --all
   git push --force --tags
   ```

3. **Wymuś reset haseł** kont, które są w tym pliku. Hashe bcrypt są odporne
   na szybkie łamanie, ale słabe hasła (`password`) łamie się natychmiast —
   `php artisan security:audit --fix` załatwia znane przypadki.

4. Po wyczyszczeniu historii na serwerze zrób świeży klon zamiast `git pull`.

> **Zanim wdrożysz:** `git pull` usunie `ogloszenia_db` z serwera. Sprawdź, że
> produkcja korzysta z MySQL, a nie z tego pliku:
> `grep DB_CONNECTION /var/www/projekciarz.pl/.env` — ma być `mysql`.

### Krok 3 — konta i sekrety

W repozytorium było jawne hasło administratora (`Projekciarz_123!!!` dla
`biuro@cleancodeas.pl`) oraz konta demo z hasłem `password`. Jeśli seeder
kiedykolwiek poszedł na produkcji, te konta istnieją.

```bash
php artisan security:audit          # tylko raport
php artisan security:audit --fix    # usuwa konta demo, resetuje wyciekłe hasła
```

Następnie zrotuj sekrety w `.env`:

- `DB_PASSWORD` — nowe hasło użytkownika bazy
- `MAIL_PASSWORD` — nowe hasło aplikacji Gmail
- `RECAPTCHA_SECRET_KEY` — nowy sekret w konsoli reCAPTCHA
- klucze OpenAI / Unsplash — wygeneruj nowe i wpisz w `/admin/blog/generator`

`APP_KEY` **zostaw bez zmian** — jego zmiana unieważnia wszystkie sesje
i uniemożliwia odszyfrowanie zapisanych kluczy API.

### Krok 4 — wdrożenie kodu

```bash
cd /var/www/projekciarz.pl
sudo ./deploy.sh
```

Skrypt sam ustawia poprawne uprawnienia (644 pliki / 755 katalogi / 600 dla `.env`),
czyści cache, uruchamia migracje i na koniec audyt bezpieczeństwa.

### Krok 5 — uzupełnij `.env` na produkcji

Nowe zmienne wprowadzone w tej wersji:

```dotenv
SECURITY_CSP_ENABLED=true
SECURITY_CSP_REPORT_ONLY=false
REQUIRE_EMAIL_VERIFICATION=false
TRUSTED_PROXIES=127.0.0.1,::1
ADMIN_EMAIL=twoj-email@domena.pl
ADMIN_PASSWORD=
```

Upewnij się też, że są ustawione:

```dotenv
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...
```

Po każdej zmianie `.env`: `php artisan config:cache`.

### Krok 6 — cron dla schedulera

Bez tego blog AI, newsletter i przypomnienia mailowe nie działają w ogóle.

```bash
sudo crontab -u www-data -e
```

```cron
* * * * * cd /var/www/projekciarz.pl && php artisan schedule:run >> /dev/null 2>&1
```

### Krok 7 — weryfikacja

```bash
php artisan security:audit
php artisan test
curl -sI https://projekciarz.pl | grep -i "content-security-policy\|strict-transport"
```

W przeglądarce: zaloguj się jako zwykły użytkownik i wejdź na `/admin/users`.
Musisz dostać **403**.

---

## 2. Co zostało zmienione w kodzie

| Obszar | Problem | Rozwiązanie |
|---|---|---|
| Panel admina | grupa `/admin/*` chroniona tylko przez `auth` — każdy zalogowany mógł nadać sobie rolę admina | middleware `EnsureUserIsAdmin` na całej grupie |
| Biblioteka mediów | brak walidacji typu — wgranie `.php` = wykonanie kodu na serwerze | reguła `SafeUpload` + allowlista rozszerzeń + blokada w nginx |
| Avatary, portfolio, blog | reguła `image` Laravela dopuszcza SVG (nośnik XSS) | jawna lista `mimes:` + `SafeUpload` |
| Publiczne API | `Announcement::with('user')` zwracało e-mail i telefon każdego wystawiającego | pola prywatne w `$hidden`, jawne `withPrivateFields()` tam, gdzie użytkownik ogląda swoje dane |
| Blog / strony CMS | `{!! $content !!}` renderował surowy HTML z generatora AI | `HtmlSanitizer` (allowlista tagów) przy zapisie i przy odczycie |
| Klucze API | OpenAI i Unsplash trzymane jawnie w bazie i drukowane w HTML | szyfrowanie `APP_KEY`, w formularzu tylko maska |
| Rate limiting | `/api` bez żadnego limitu (w Laravel 11+ `throttle:api` nie jest domyślny) | `throttleApi('api')` + limiter 40/min gość, 120/min zalogowany |
| Nagłówki HTTP | brak CSP, HSTS, X-Frame-Options | middleware `SecurityHeaders`, konfigurowalny w `config/security.php` |
| `deploy.sh` | `chmod -R 755` na całym projekcie otwierał `.env` na odczyt dla systemu | 644/755 rozdzielnie, `.env` na 600, tryb konserwacji, `queue:restart` |
| Seeder | hasło admina zapisane jawnie w repozytorium | hasło z `.env` lub losowe; konta demo pomijane na produkcji |
| Rejestracja API | ustawiała nieistniejącą rolę `user` | walidacja `in:client,freelancer`, domyślnie `client` |
| Repozytorium | `.env.*`, zrzuty bazy i pliki `.sql` mogły trafić do gita | uszczelniony `.gitignore` + `.gitattributes` (koniec z CRLF) |

Nowe pliki:

- `app/Http/Middleware/EnsureUserIsAdmin.php`
- `app/Http/Middleware/SecurityHeaders.php`
- `app/Rules/SafeUpload.php`
- `app/Services/HtmlSanitizer.php`
- `app/Console/Commands/SecurityAudit.php`
- `config/security.php`
- `deploy/nginx/projekciarz.pl.conf`
- `tests/Feature/AdminAccessTest.php`, `ApiPrivacyTest.php`, `MediaUploadTest.php`
- `tests/Unit/HtmlSanitizerTest.php`

---

## 3. Rozwiązywanie problemów po wdrożeniu

**Strona wygląda na rozjechaną, w konsoli błędy CSP**
Któryś zewnętrzny skrypt nie jest na allowliście.

```dotenv
SECURITY_CSP_REPORT_ONLY=true
```

Odśwież stronę, spisz zablokowane hosty z konsoli przeglądarki, dopisz je
w `config/security.php` → `csp_sources`, wróć na `false`.
Awaryjnie: `SECURITY_CSP_ENABLED=false`.

**Nie mogę wejść do panelu jako admin**

```bash
php artisan security:audit    # sprawdź, czy konto ma role=admin i is_approved=1
```

**Formularz odrzuca poprawny obrazek**
Rozszerzenie spoza `config/security.uploads.image_mimes`. Dopisz je tam,
ale **nigdy** nie dodawaj `svg` ani niczego wykonywalnego.

**Stary wpis blogowy stracił formatowanie**
Sanitizer usunął tag spoza allowlisty. Lista dozwolonych tagów jest
w `HtmlSanitizer::ALLOWED_TAGS`.

### Błąd 500

```bash
cd /var/www/projekciarz.pl

# 1. Co dokładnie się stało
sudo tail -n 100 storage/logs/laravel.log
sudo tail -n 50 /var/log/nginx/error.log
sudo tail -n 50 /var/log/php8.2-fpm.log

# 2. Najczęstsza przyczyna po deployu - nieaktualny cache
sudo -u www-data php artisan optimize:clear
sudo -u www-data composer dump-autoload
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo systemctl restart php8.2-fpm

# 3. Błąd składni po ręcznej edycji na serwerze
find app -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
```

> **Nigdy nie włączaj `APP_DEBUG=true` ani `APP_ENV=local` na produkcji.**
> Debug wyświetla zawartość `.env` na stronie błędu, a `APP_ENV=local`
> dodatkowo **wyłącza weryfikację reCAPTCHA** przy logowaniu i rejestracji
> (`RecaptchaService` przepuszcza brak sekretu tylko w `local`/`testing`).
> Przyczynę błędu znajdziesz w `storage/logs/laravel.log` — tam jest ten sam
> stack trace, tylko bez pokazywania go całemu internetowi.

### Git odmawia `pull`: „detected dubious ownership"

```bash
sudo chown -R www-data:www-data /var/www/projekciarz.pl
sudo -u www-data git config --global --add safe.directory /var/www/projekciarz.pl
```

Nie ustawiaj `safe.directory '*'` — wyłącza tę ochronę dla wszystkich
repozytoriów w systemie.

### Przeniesienie treści z produkcji na lokalne środowisko

Produkcja stoi na MySQL, lokalnie jest SQLite, więc zrzut `.sql` się nie przeniesie —
najprościej przez JSON. Dotyczy stron CMS i wpisów blogowych.

```bash
# NA SERWERZE
cd /var/www/projekciarz.pl
sudo -u www-data php artisan tinker --execute="
file_put_contents('/tmp/pages.json', \App\Models\Page::all()->toJson());
file_put_contents('/tmp/posts.json', \App\Models\BlogPost::all()->toJson());
echo \App\Models\Page::count() . ' stron, ' . \App\Models\BlogPost::count() . ' wpisów';
"

# LOKALNIE
scp uzytkownik@projekciarz.pl:/tmp/pages.json .
scp uzytkownik@projekciarz.pl:/tmp/posts.json .

php artisan tinker --execute="
foreach (json_decode(file_get_contents('pages.json'), true) as \$p) {
    unset(\$p['id'], \$p['created_at'], \$p['updated_at']);
    \App\Models\Page::updateOrCreate(['slug' => \$p['slug']], \$p);
}
foreach (json_decode(file_get_contents('posts.json'), true) as \$p) {
    unset(\$p['id'], \$p['created_at'], \$p['updated_at']);
    \App\Models\BlogPost::updateOrCreate(['slug' => \$p['slug']], \$p);
}
echo 'Zaimportowano';
"
```

Pełna kopia bazy produkcyjnej (przed każdą ryzykowną zmianą):

```bash
mysqldump -u UZYTKOWNIK -p NAZWA_BAZY | gzip > backup_$(date +%Y%m%d_%H%M).sql.gz
```

> W projekcie **nie ma** komend `db:backup`, `db:export` ani `db:import` —
> jedyne dostępne komendy zobaczysz przez `php artisan list`.

---

## 4. Do zrobienia w następnej kolejności

Rzeczy, które nie blokują wdrożenia, ale warto je domknąć:

1. **Aktualizacja zależności** — `composer update && composer audit`,
   `npm update && npm audit`. Wersje w lockfile są z listopada 2025.
2. **Skrypty z CDN** — `cdn.tailwindcss.com` i `unpkg.com` w layoutach to
   wersje deweloperskie ładowane z cudzych serwerów. Projekt ma już Vite,
   więc warto je zbundlować i usunąć CDN-y (mniej ryzyka i szybsze ładowanie).
3. **Weryfikacja e-mail** — po backfillu `users.email_verified_at`
   ustaw `REQUIRE_EMAIL_VERIFICATION=true`.
4. **API** — aplikacja jest full-stack (Livewire), a `routes/api.php`
   wygląda na pozostałość po osobnym froncie. Jeśli nic z niego nie korzysta,
   najbezpieczniej wyciąć plik w całości.
5. **Sitemap** — `robots.txt` wskazuje na `/sitemap.xml`, którego jeszcze nie ma.
6. **Kopie zapasowe bazy** — na razie brak jakiejkolwiek automatyzacji.
