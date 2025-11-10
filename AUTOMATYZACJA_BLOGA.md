# 🤖 Automatyzacja Bloga - Pełna Instrukcja

## ✨ Co zostało dodane:

1. **🧠 AI Generowanie treści** - Gemini API (Google) - **DARMOWE!**
2. **📸 Automatyczne obrazki** - Unsplash API - **DARMOWE!**
3. **🏷️ Automatyczne tagowanie** - na podstawie tytułu
4. **⏰ Scheduler** - codziennie o 9:00
5. **✅ Test mode** - przed publikacją

---

## 🔑 Krok 1: Pobierz darmowe klucze API

### A) Gemini API (Google) - do generowania treści

1. Wejdź na: **https://makersuite.google.com/app/apikey**
2. Zaloguj się kontem Google
3. Kliknij **"Create API Key"**
4. Skopiuj klucz

**Darmowy tier:**
- ✅ 60 requestów/minutę
- ✅ 1500 requestów/dzień
- ✅ Bezterminowo darmowe

### B) Unsplash API - do obrazków (opcjonalne)

1. Wejdź na: **https://unsplash.com/developers**
2. Załóż konto i zaloguj się
3. Kliknij **"New Application"**
4. Zaakceptuj warunki
5. Nazwij aplikację (np. "WebFreelance Blog")
6. Skopiuj **"Access Key"**

**Darmowy tier:**
- ✅ 50 requestów/godzinę
- ✅ Bezterminowo darmowe
- Jeśli nie dodasz klucza - wyświetli się gradient (też wygląda dobrze!)

---

## 🔧 Krok 2: Dodaj klucze do .env

Otwórz plik `backend/.env` i dodaj na końcu:

```env
# 🤖 Automatyzacja bloga
GEMINI_API_KEY=twoj_klucz_gemini_tutaj
UNSPLASH_ACCESS_KEY=twoj_klucz_unsplash_tutaj
```

**Przykład:**
```env
GEMINI_API_KEY=AIzaSyDk8gHxN9fME-Xa1b2cD3e4F5g6H7i8J9k
UNSPLASH_ACCESS_KEY=abc123def456ghi789jkl012mno345pqr678stu901
```

---

## ⚙️ Krok 3: Skonfiguruj Cron (na serwerze)

### Dla lokalnego testowania (Windows):

Nie potrzebujesz crona - możesz uruchomić ręcznie:

```bash
php artisan blog:generate --test
```

### Dla serwera produkcyjnego (Linux):

1. Otwórz crontab:
```bash
crontab -e
```

2. Dodaj linię (zastąp ścieżkę):
```bash
* * * * * cd /sciezka/do/projektu/backend && php artisan schedule:run >> /dev/null 2>&1
```

3. Zapisz (Ctrl+O, Enter, Ctrl+X)

**Laravel automatycznie uruchomi `blog:generate` codziennie o 9:00!**

---

## 🎯 Krok 4: Testowanie

### Test lokalny (bez publikacji):

```bash
cd backend
php artisan blog:generate --test
```

Co się stanie:
- ✅ Wygeneruje treść przez Gemini AI
- ✅ Pobierze obrazek z Unsplash
- ✅ Przypisze tagi
- ⚠️ **Utworzy SZKIC** (nie opublikuje automatycznie)

### Publikacja natychmiastowa:

```bash
php artisan blog:generate
```

Co się stanie:
- ✅ Wszystko jak wyżej
- ✅ **Automatycznie OPUBLIKUJE** wpis

---

## 📅 Harmonogram automatyzacji

**Domyślnie:** Codziennie o **9:00** (strefa Europa/Warsaw)

### Zmiana godziny:

Edytuj `backend/routes/console.php`:

```php
Schedule::command('blog:generate')
    ->dailyAt('15:00')  // Zmień na inną godzinę (format 24h)
```

### Inne opcje harmonogramu:

```php
->daily()              // Raz dziennie o północy
->dailyAt('09:00')     // Codziennie o 9:00
->twiceDaily(9, 15)    // Dwa razy dziennie (9:00 i 15:00)
->weekdays()           // Tylko dni robocze
->mondays()            // Tylko poniedziałki
->hourly()             // Co godzinę
->everyThreeHours()    // Co 3 godziny
```

---

## 🎨 Dostosowanie tematów

Edytuj `backend/app/Console/Commands/GenerateBlogPost.php`:

```php
private $topics = [
    'Twój własny temat 1',
    'Twój własny temat 2',
    'Twój własny temat 3',
    // ... dodaj więcej
];
```

**Wskazówki:**
- Konkretne tematy dają lepsze wyniki
- Dostosuj do tematyki freelancingu/twojej niszy
- Minimum 10-15 tematów dla różnorodności

---

## 📊 Monitorowanie

### Zobacz logi:

```bash
tail -f storage/logs/laravel.log
```

### Sprawdź ostatnie wpisy:

```bash
php artisan tinker
>>> BlogPost::latest()->limit(5)->get(['title', 'created_at'])
```

### Ręczne uruchomienie schedulera:

```bash
php artisan schedule:run
```

---

## 🛠️ Rozwiązywanie problemów

### ❌ "Brak GEMINI_API_KEY w .env"

**Rozwiązanie:** Dodaj klucz do pliku `.env`:
```env
GEMINI_API_KEY=twoj_klucz_tutaj
```

### ❌ "Błąd API: 401 Unauthorized"

**Rozwiązanie:** Klucz API jest nieprawidłowy. Wygeneruj nowy na:
https://makersuite.google.com/app/apikey

### ❌ "Timeout"

**Rozwiązanie:** Generowanie może trwać 10-30 sekund. To normalne!

### ⚠️ "Nie pobrano obrazka"

**Rozwiązanie:** To OK! Wyświetli się gradient. Jeśli chcesz obrazki, dodaj `UNSPLASH_ACCESS_KEY`.

### ❌ Scheduler nie działa

**Windows (local):**
- Scheduler wymaga crona. Uruchamiaj ręcznie: `php artisan blog:generate`

**Linux (production):**
- Sprawdź crontab: `crontab -l`
- Sprawdź logi: `grep CRON /var/log/syslog`

---

## 💡 Pro Tips

### 1. **Różnorodność treści**
Dodaj więcej tematów (minimum 20) dla lepszej różnorodności.

### 2. **Edycja przed publikacją**
Użyj `--test` żeby sprawdzić treść przed publikacją:
```bash
php artisan blog:generate --test
```
Potem edytuj w panelu admina i opublikuj ręcznie.

### 3. **Paczka wpisów**
Wygeneruj kilka wpisów naraz:
```bash
for i in {1..5}; do php artisan blog:generate --test; done
```

### 4. **Customizacja AI**
Edytuj prompt w `GenerateBlogPost.php` aby dostosować styl/ton treści.

---

## 📈 Statystyki

### Co zostanie wygenerowane:

- **Tytuł:** SEO-friendly, 50-60 znaków
- **Zajawka:** 150-160 znaków
- **Treść:** 800-1200 słów, pełen HTML
- **Meta title:** Maks 60 znaków
- **Meta description:** Maks 160 znaków
- **Słowa kluczowe:** 5-7 słów
- **Obrazek:** 1200x630px (Unsplash) lub gradient
- **Tagi:** Automatycznie przypisane

---

## 🚀 Gotowe!

**Automatyzacja skonfigurowana!** 🎉

Każdego dnia o 9:00 nowy wpis pojawi się automatycznie na blogu.

**Testuj najpierw:**
```bash
php artisan blog:generate --test
```

**Pytania?** Sprawdź logi w `storage/logs/laravel.log`

---

**Koszt: 0 PLN/miesiąc** ✅

