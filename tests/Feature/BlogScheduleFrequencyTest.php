<?php

namespace Tests\Feature;

use App\Models\BlogSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Regresja: harmonogram leci co minutę (routes/console.php), więc każda
 * częstotliwość musi mieć jawną blokadę czasu - inaczej np. "raz w tygodniu"
 * generowałoby wpis co minutę przez cały dzień.
 */
class BlogScheduleFrequencyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function schedule(array $attributes): BlogSchedule
    {
        return BlogSchedule::create(array_merge([
            'is_enabled' => true,
            'time' => '09:00',
            'count' => 1,
            'topics' => "Temat testowy",
        ], $attributes));
    }

    public function test_weekly_nie_odpala_sie_poza_zaplanowana_minuta(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05 09:01'));

        $this->schedule(['frequency' => 'weekly']);

        $this->artisan('blog:run-scheduled')
            ->expectsOutputToContain('Nie jest jeszcze czas');
    }

    public function test_weekly_nie_odpala_sie_za_wczesnie_po_ostatnim_uruchomieniu(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05 09:00'));

        $this->schedule([
            'frequency' => 'weekly',
            'last_run_at' => Carbon::parse('2026-01-02 09:00'),
        ]);

        $this->artisan('blog:run-scheduled')
            ->expectsOutputToContain('Nie jest jeszcze czas');
    }

    public function test_every_3_days_odpala_sie_po_3_dniach_o_wlasciwej_godzinie(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05 09:00'));

        $this->schedule([
            'frequency' => 'every_3_days',
            'last_run_at' => Carbon::parse('2026-01-02 09:00'),
        ]);

        $this->artisan('blog:run-scheduled')
            ->doesntExpectOutputToContain('Nie jest jeszcze czas');
    }

    public function test_weekdays_nie_odpala_sie_w_weekend(): void
    {
        // 2026-01-03 to sobota
        Carbon::setTestNow(Carbon::parse('2026-01-03 09:00'));

        $this->schedule(['frequency' => 'weekdays']);

        $this->artisan('blog:run-scheduled')
            ->expectsOutputToContain('Nie jest jeszcze czas');
    }

    public function test_twice_daily_odpala_sie_takze_12h_po_zaplanowanej_godzinie(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-05 21:00'));

        $this->schedule(['frequency' => 'twice_daily', 'time' => '09:00']);

        $this->artisan('blog:run-scheduled')
            ->doesntExpectOutputToContain('Nie jest jeszcze czas');
    }
}
