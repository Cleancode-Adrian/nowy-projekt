# 🤖 Konfiguracja automatyzacji bloga
Write-Host "🤖 Konfiguracja automatyzacji bloga" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Sprawdź czy .env istnieje
if (-not (Test-Path ".env")) {
    Write-Host "❌ Plik .env nie istnieje!" -ForegroundColor Red
    exit 1
}

Write-Host "📝 Dodaj klucze API do pliku .env" -ForegroundColor Yellow
Write-Host ""

# Gemini API
$gemini_key = Read-Host "Wklej klucz GEMINI_API_KEY (lub Enter aby pominąć)"
if ($gemini_key) {
    $envContent = Get-Content .env
    if ($envContent -match "GEMINI_API_KEY") {
        $envContent = $envContent -replace "GEMINI_API_KEY=.*", "GEMINI_API_KEY=$gemini_key"
    } else {
        $envContent += "`n# 🤖 Automatyzacja bloga"
        $envContent += "`nGEMINI_API_KEY=$gemini_key"
    }
    $envContent | Set-Content .env
    Write-Host "✅ GEMINI_API_KEY dodany" -ForegroundColor Green
}

# Unsplash API
$unsplash_key = Read-Host "Wklej klucz UNSPLASH_ACCESS_KEY (lub Enter aby pominąć)"
if ($unsplash_key) {
    $envContent = Get-Content .env
    if ($envContent -match "UNSPLASH_ACCESS_KEY") {
        $envContent = $envContent -replace "UNSPLASH_ACCESS_KEY=.*", "UNSPLASH_ACCESS_KEY=$unsplash_key"
    } else {
        if (-not ($envContent -match "GEMINI_API_KEY")) {
            $envContent += "`n# 🤖 Automatyzacja bloga"
        }
        $envContent += "`nUNSPLASH_ACCESS_KEY=$unsplash_key"
    }
    $envContent | Set-Content .env
    Write-Host "✅ UNSPLASH_ACCESS_KEY dodany" -ForegroundColor Green
}

Write-Host ""
Write-Host "🎯 Konfiguracja zakończona!" -ForegroundColor Green
Write-Host ""
Write-Host "📖 Przeczytaj pełną instrukcję: AUTOMATYZACJA_BLOGA.md" -ForegroundColor Yellow
Write-Host ""
Write-Host "🧪 Test command (tryb testowy):" -ForegroundColor Cyan
Write-Host "   php artisan blog:generate --test"
Write-Host ""
Write-Host "🚀 Generuj wpis (publikacja):" -ForegroundColor Cyan
Write-Host "   php artisan blog:generate"

