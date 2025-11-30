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
