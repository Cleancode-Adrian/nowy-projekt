<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\BlogGeneratorController;
use App\Models\BlogSchedule;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunScheduledBlogPosts extends Command
{
    protected $signature = 'blog:run-scheduled';
    protected $description = 'Uruchamia zaplanowane generowanie wpisów blogowych';

    public function handle()
    {
        $schedule = BlogSchedule::where('is_enabled', true)->first();

        if (!$schedule) {
            $this->info('ℹ️ Brak aktywnego harmonogramu');
            return 0;
        }

        $this->info("📅 Uruchamiam harmonogram: {$schedule->frequency} o {$schedule->time}");

        // Sprawdź czy to właściwy czas
        $now = now();
        $scheduleTime = now()->setTimeFromTimeString($schedule->time);

        // Dla daily - sprawdź czy to właściwa godzina
        if ($schedule->frequency === 'daily') {
            if ($now->format('H:i') !== $scheduleTime->format('H:i')) {
                $this->info("⏰ Nie jest jeszcze czas (aktualnie: {$now->format('H:i')}, zaplanowane: {$schedule->time})");
                return 0;
            }
        }

        // Przygotuj tematy
        $topics = $schedule->topics 
            ? array_filter(array_map('trim', explode("\n", $schedule->topics)))
            : [];

        if (empty($topics)) {
            $this->error('❌ Brak tematów w harmonogramie');
            return 1;
        }

        // Wybierz losowe tematy
        $selectedTopics = array_slice(
            (array) array_rand(array_flip($topics), min($schedule->count, count($topics))),
            0,
            $schedule->count
        );

        if (!is_array($selectedTopics)) {
            $selectedTopics = [$selectedTopics];
        }

        $this->info("📝 Generuję " . count($selectedTopics) . " wpisów...");

        $openaiKey = Setting::where('key', 'openai_api_key')->value('value');
        
        if (!$openaiKey) {
            $this->error('❌ Brak klucza OpenAI API');
            return 1;
        }

        // Uruchom generowanie dla każdego tematu
        $controller = new BlogGeneratorController();
        
        foreach ($selectedTopics as $topic) {
            $this->info("  → {$topic}");
            
            $request = Request::create('/admin/blog/generator/generate', 'POST', [
                'topics' => $topic,
                'count' => 1,
                'category_id' => $schedule->category_id,
                'tags' => $schedule->tags,
                'download_image' => $schedule->download_image,
                'test_mode' => !$schedule->auto_publish,
            ]);
            
            try {
                $controller->generate($request);
            } catch (\Exception $e) {
                $this->error("  ❌ Błąd: " . $e->getMessage());
            }
        }

        // Zaktualizuj last_run_at
        $schedule->update(['last_run_at' => now()]);

        $this->info("✅ Harmonogram wykonany pomyślnie!");
        return 0;
    }
}

