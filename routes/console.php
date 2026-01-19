<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🤖 Automatyczne generowanie wpisów blogowych
Schedule::command('blog:run-scheduled')
    ->everyMinute() // Sprawdza co minutę czy to właściwy czas
    ->timezone('Europe/Warsaw')
    ->onSuccess(function () {
        info('✅ Zaplanowane wpisy blogowe wygenerowane');
    })
    ->onFailure(function () {
        info('❌ Błąd generowania zaplanowanych wpisów blogowych');
    });

// 📧 Powiadomienia o pasujących ogłoszeniach (codziennie o 9:00)
Schedule::command('announcements:notify-matching')
    ->dailyAt('09:00')
    ->timezone('Europe/Warsaw');

// 📧 Cotygodniowy newsletter (poniedziałek o 8:00)
Schedule::command('newsletter:send-weekly')
    ->weeklyOn(1, '8:00')
    ->timezone('Europe/Warsaw');

// 📊 Cotygodniowe podsumowania (poniedziałek o 9:00)
Schedule::command('users:send-weekly-summary')
    ->weeklyOn(1, '9:00')
    ->timezone('Europe/Warsaw');

// ⏰ Przypomnienia o nieaktywnych projektach (codziennie o 10:00)
Schedule::command('projects:remind-inactive')
    ->dailyAt('10:00')
    ->timezone('Europe/Warsaw');