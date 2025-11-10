#!/bin/bash

echo "🤖 Konfiguracja automatyzacji bloga"
echo "====================================="
echo ""

# Sprawdź czy .env istnieje
if [ ! -f .env ]; then
    echo "❌ Plik .env nie istnieje!"
    exit 1
fi

echo "📝 Dodaj klucze API do pliku .env"
echo ""

# Gemini API
read -p "Wklej klucz GEMINI_API_KEY (lub Enter aby pominąć): " gemini_key
if [ ! -z "$gemini_key" ]; then
    if grep -q "GEMINI_API_KEY" .env; then
        sed -i "s/GEMINI_API_KEY=.*/GEMINI_API_KEY=$gemini_key/" .env
    else
        echo "" >> .env
        echo "# 🤖 Automatyzacja bloga" >> .env
        echo "GEMINI_API_KEY=$gemini_key" >> .env
    fi
    echo "✅ GEMINI_API_KEY dodany"
fi

# Unsplash API
read -p "Wklej klucz UNSPLASH_ACCESS_KEY (lub Enter aby pominąć): " unsplash_key
if [ ! -z "$unsplash_key" ]; then
    if grep -q "UNSPLASH_ACCESS_KEY" .env; then
        sed -i "s/UNSPLASH_ACCESS_KEY=.*/UNSPLASH_ACCESS_KEY=$unsplash_key/" .env
    else
        if ! grep -q "GEMINI_API_KEY" .env; then
            echo "" >> .env
            echo "# 🤖 Automatyzacja bloga" >> .env
        fi
        echo "UNSPLASH_ACCESS_KEY=$unsplash_key" >> .env
    fi
    echo "✅ UNSPLASH_ACCESS_KEY dodany"
fi

echo ""
echo "🎯 Konfiguracja zakończona!"
echo ""
echo "📖 Przeczytaj pełną instrukcję: AUTOMATYZACJA_BLOGA.md"
echo ""
echo "🧪 Test command (tryb testowy):"
echo "   php artisan blog:generate --test"
echo ""
echo "🚀 Generuj wpis (publikacja):"
echo "   php artisan blog:generate"

