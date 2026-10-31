# Projekciarz.pl

Platforma ogłoszeń łącząca klientów z wykonawcami projektów. Publikowanie zleceń,
zbieranie ofert, wiadomości, oceny i reputacja.

Produkcja: **https://projekciarz.pl**

---

## Stack

- **Laravel 12** + **Livewire 3** (aplikacja full-stack, renderowanie po stronie serwera)
- **MySQL 8** na produkcji, SQLite lokalnie
- **Tailwind CSS 4** + **Alpine.js**, build przez **Vite 8**
- **Laravel Sanctum** — tokeny dla `routes/api.php`
- **Laravel Reverb** — powiadomienia i czat na żywo (własny serwer WebSocket)
- PHP 8.2+, nginx + php-fpm

---

## Uruchomienie lokalne

```bash
composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

php artisan serve
php artisan reverb:start   # w osobnym terminalu - powiadomienia/czat na żywo
```

Aplikacja: http://127.0.0.1:8000

Konto administratora tworzy seeder na podstawie `ADMIN_EMAIL` / `ADMIN_PASSWORD`
z pliku `.env`. Jeśli `ADMIN_PASSWORD` jest puste, seeder wygeneruje losowe hasło
i wypisze je **jeden raz** w konsoli.

Konta demonstracyjne (`client@example.com`, `freelancer@example.com`, `jan@example.com`,
hasło `password`) powstają wyłącznie poza środowiskiem produkcyjnym.

### Praca na żywo

```bash
composer dev     # serwer + kolejka + logi + vite jednocześnie
```

---

## Testy

```bash
php artisan test
```

Testy bezpieczeństwa pilnują regresji, które kiedyś realnie wystąpiły:

- `tests/Feature/AdminAccessTest.php` — panel admina niedostępny dla zwykłych kont
- `tests/Feature/ApiPrivacyTest.php` — publiczne API nie zwraca e-maili ani telefonów
- `tests/Feature/MediaUploadTest.php` — biblioteka mediów odrzuca pliki wykonywalne
- `tests/Unit/HtmlSanitizerTest.php` — sanityzacja treści bloga i stron CMS

---

## Funkcje

**Klient:** dodawanie i edycja ogłoszeń, przeglądanie ofert, akceptowanie
i odrzucanie propozycji, wiadomości, wystawianie ocen.

**Wykonawca:** przeglądanie i filtrowanie ogłoszeń, wysyłanie ofert, panel „Moje oferty”,
portfolio, wiadomości.

**Wspólne:** zapisane ogłoszenia, profil publiczny z portfolio i statystykami,
oceny i opinie, wyszukiwarka zaawansowana, powiadomienia in-app i mailowe,
zgłaszanie treści, FAQ, odznaki, załączniki do ogłoszeń, ranking wykonawców,
weryfikacja adresu e-mail.

**Panel administratora:** moderacja użytkowników, ogłoszeń i opinii, blog
z generatorem AI, edytor stron, biblioteka mediów.

**Blog:** wpisy generowane automatycznie (Gemini / OpenAI + Unsplash),
publikowane przez Laravel Scheduler.

---

## Wdrożenie

Procedura wdrożenia, konfiguracja nginx i checklista bezpieczeństwa:
**[BEZPIECZENSTWO.md](BEZPIECZENSTWO.md)**

W skrócie — repozytorium jest sklonowane bezpośrednio do `/var/www/projekciarz.pl`:

```bash
cd /var/www/projekciarz.pl
sudo ./deploy.sh
```

Webroot nginx musi wskazywać na `/var/www/projekciarz.pl/public`, nigdy na korzeń
repozytorium — inaczej `.env` i `.git` są publicznie dostępne.

Scheduler wymaga wpisu w cronie — bez niego blog i maile cykliczne nie działają:

```cron
* * * * * cd /var/www/projekciarz.pl && php artisan schedule:run >> /dev/null 2>&1
```

---

## Dokumentacja

Cała dokumentacja operacyjna jest w jednym miejscu:
**[BEZPIECZENSTWO.md](BEZPIECZENSTWO.md)** — wdrożenie, konfiguracja nginx,
audyt bezpieczeństwa, rozwiązywanie problemów (błąd 500, CSP, git, przenoszenie
treści z produkcji na lokalne środowisko).

---

## Licencja

MIT
