# 🤖 Generator Wpisów Blogowych z OpenAI

## 📋 Dostępne komendy

### 1. Naprawa brakujących zdjęć
```bash
php artisan blog:fix-images
php artisan blog:fix-images --limit=20
```

### 2. Generowanie pojedynczego wpisu z OpenAI
```bash
# Z losowym tematem
php artisan blog:generate-openai

# Z konkretnym tematem
php artisan blog:generate-openai "Jak znaleźć klientów jako freelancer"

# Z obrazkiem
php artisan blog:generate-openai --image

# Tryb testowy (szkic)
php artisan blog:generate-openai --test

# Z kategorią i tagami
php artisan blog:generate-openai --category=1 --tags="Freelancing,Marketing"
```

### 3. Masowe generowanie wpisów
```bash
# Wygeneruj 5 wpisów
php artisan blog:generate-openai --count=5

# Wygeneruj 10 wpisów z obrazkami
php artisan blog:generate-openai --count=10 --image

# Wygeneruj 3 wpisy w trybie testowym
php artisan blog:generate-openai --count=3 --test
```

## 🔑 Konfiguracja

### 1. OpenAI API Key (wymagane)
Dodaj do `.env`:
```env
OPENAI_API_KEY=sk-...
```

**Jak uzyskać klucz:**
1. Przejdź na https://platform.openai.com/api-keys
2. Zaloguj się lub utwórz konto
3. Kliknij "Create new secret key"
4. Skopiuj klucz i dodaj do `.env`

**Koszty:**
- GPT-4o-mini: ~$0.15 za 1M tokenów wejściowych, ~$0.60 za 1M tokenów wyjściowych
- Jeden wpis blogowy (~1500 słów): ~$0.01-0.02

### 2. Unsplash API Key (opcjonalne, dla lepszych zdjęć)
Dodaj do `.env`:
```env
UNSPLASH_ACCESS_KEY=...
```

**Jak uzyskać klucz:**
1. Przejdź na https://unsplash.com/developers
2. Utwórz aplikację (darmowe)
3. Skopiuj Access Key

**Bez klucza:**
- Zdjęcia będą pobierane z Unsplash Source (darmowe, ale mniej kontroli)

## 📝 Przykłady użycia

### Przykład 1: Szybkie wygenerowanie wpisu
```bash
php artisan blog:generate-openai "Automatyzacja marketingu dla freelancerów" --image
```

### Przykład 2: Masowe generowanie z kategoriami
```bash
# Wygeneruj 5 wpisów o automatyzacji
php artisan blog:generate-openai --count=5 --category=1 --tags="Automatyzacja,AI" --image
```

### Przykład 3: Test przed publikacją
```bash
# Wygeneruj szkic do sprawdzenia
php artisan blog:generate-openai "Nowy temat" --test --image

# Jeśli OK, usuń --test i wygeneruj ponownie
php artisan blog:generate-openai "Nowy temat" --image
```

## 🎯 Automatyczne funkcje

Generator automatycznie:
- ✅ Tworzy SEO-friendly tytuły i opisy
- ✅ Generuje treść w HTML (h2, h3, listy, tabele)
- ✅ Wybiera odpowiednie tagi na podstawie tematu
- ✅ Przypisuje kategorię (lub używa domyślnej)
- ✅ Tworzy unikalne slugi
- ✅ Dodaje call-to-action do Projekciarz.pl
- ✅ Generuje meta keywords
- ✅ Pobiera odpowiednie zdjęcia z Unsplash

## 🔧 Rozwiązywanie problemów

### Błąd: "Brak OPENAI_API_KEY"
- Sprawdź czy klucz jest w `.env`
- Uruchom `php artisan config:clear`

### Błąd: "Nieprawidłowa odpowiedź AI"
- Sprawdź czy masz środki na koncie OpenAI
- Spróbuj ponownie (czasami API zwraca błąd)

### Brak zdjęć
- Dodaj `UNSPLASH_ACCESS_KEY` do `.env`
- Lub użyj `--image` (zdjęcia z Unsplash Source)

### Zbyt długie generowanie
- To normalne - generowanie treści trwa 30-60 sekund
- Użyj `--test` do szybkiego testowania

## 💡 Wskazówki

1. **Zacznij od testów**: Użyj `--test` przed masowym generowaniem
2. **Sprawdzaj jakość**: Przeczytaj wygenerowane wpisy przed publikacją
3. **Dostosuj tematy**: Edytuj `$defaultTopics` w `GenerateBlogPostOpenAI.php`
4. **Używaj tagów**: Dodaj `--tags` dla lepszej kategoryzacji
5. **Obrazki**: Zawsze używaj `--image` dla lepszego SEO

## 📊 Statystyki

- **Czas generowania**: ~30-60 sekund na wpis
- **Długość treści**: 1000-1500 słów
- **Koszt**: ~$0.01-0.02 za wpis (GPT-4o-mini)
- **Jakość**: Wysoka, gotowa do publikacji po szybkiej korekcie

## 🚀 Automatyzacja

Możesz dodać do cron dla automatycznego generowania:
```bash
# Generuj 1 wpis dziennie o 9:00
0 9 * * * cd /var/www/projekciarz.pl && php artisan blog:generate-openai --image
```

