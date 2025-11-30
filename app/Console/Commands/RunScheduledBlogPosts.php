<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\BlogSchedule;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        $admin = \App\Models\User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->error('❌ Brak użytkownika admin');
            return 1;
        }
        
        foreach ($selectedTopics as $topic) {
            $this->info("  → {$topic}");
            
            try {
                // Użyj komendy blog:generate-openai
                \Illuminate\Support\Facades\Artisan::call('blog:generate-openai', [
                    'topic' => $topic,
                    '--category' => $schedule->category_id,
                    '--tags' => $schedule->tags,
                    '--image' => $schedule->download_image,
                    '--test' => !$schedule->auto_publish,
                ]);
                
                $this->info("  ✅ Wygenerowano");
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

