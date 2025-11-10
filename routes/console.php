<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🤖 Automatyczne generowanie wpisów blogowych
Schedule::command('blog:generate')
    ->dailyAt('09:00')
    ->timezone('Europe/Warsaw')
    ->onSuccess(function () {
        info('✅ Blog post wygenerowany automatycznie');
    })
    ->onFailure(function () {
        info('❌ Błąd generowania blog posta');
    });
