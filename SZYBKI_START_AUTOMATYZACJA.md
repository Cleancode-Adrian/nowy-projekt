# 🚀 Szybki Start - Automatyzacja Bloga

## ⚡ W 3 krokach:

### 1️⃣ Pobierz klucz API (2 minuty)

Wejdź na: **https://makersuite.google.com/app/apikey**
- Zaloguj się Google
- Kliknij "Create API Key"
- Skopiuj klucz

### 2️⃣ Dodaj do .env

Otwórz `backend/.env` i dodaj na końcu:

```env
GEMINI_API_KEY=wklej_tutaj_swoj_klucz
```

### 3️⃣ Testuj!

```bash
cd backend
php artisan blog:generate --test
```

**GOTOWE!** 🎉

---

## 📋 Co się stanie:

✅ AI wygeneruje profesjonalny artykuł (800-1200 słów)
✅ Automatycznie pobierze obrazek lub stworzy gradient
✅ Doda tagi na podstawie tytułu
✅ Utworzy wpis jako **SZKIC** (--test) lub **OPUBLIKUJE** (bez --test)

---

## 🎯 Automatyzacja (produkcja):

**Edytuj crontab:**
```bash
crontab -e
```

**Dodaj linię:**
```bash
* * * * * cd /sciezka/do/projektu/backend && php artisan schedule:run >> /dev/null 2>&1
```

**Wpisy będą się dodawać automatycznie codziennie o 9:00!** ⏰

---

## 📖 Pełna dokumentacja:

`AUTOMATYZACJA_BLOGA.md` - szczegółowa instrukcja z rozwiązywaniem problemów

---

## 💰 Koszty:

**0 PLN/miesiąc** - wszystkie API są darmowe! ✨

**Gemini API (Google):**
- ✅ 60 requestów/minutę
- ✅ 1500 requestów/dzień
- ✅ Bezterminowo darmowe

**Unsplash API** (opcjonalne):
- ✅ 50 obrazków/godzinę
- ✅ Bezterminowo darmowe
- Bez klucza = ładny gradient (też OK!)

---

## 🎨 Przykładowy output:

**Tytuł:** "Jak znaleźć pierwszych klientów jako freelancer w 2025"

**Treść:**
- Wprowadzenie
- 5-7 sekcji z nagłówkami H2
- Listy punktowane
- Praktyczne przykłady
- Call-to-action

**SEO:**
- Meta title (60 znaków)
- Meta description (160 znaków)
- 5-7 słów kluczowych

**Obrazek:** 1200x630px (Unsplash) lub gradient

---

## 🛠️ Tryby uruchamiania:

```bash
# Test (szkic, nie publikuj)
php artisan blog:generate --test

# Publikacja natychmiastowa
php artisan blog:generate

# Wiele wpisów (5 szkiców)
for i in {1..5}; do php artisan blog:generate --test; done
```

---

## ❓ Problemy?

**Błąd API:** Sprawdź czy klucz w `.env` jest poprawny
**Timeout:** Normalne - generowanie trwa 10-30s
**Brak obrazka:** OK! Wyświetli się gradient

**Logi:** `tail -f storage/logs/laravel.log`

---

**Gotowe! Teraz Twój blog rośnie automatycznie! 🌱**

