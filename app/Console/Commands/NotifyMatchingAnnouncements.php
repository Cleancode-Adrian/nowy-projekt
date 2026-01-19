<?php

namespace App\Console\Commands;

use App\Models\SavedSearch;
use App\Mail\MatchingAnnouncementsMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotifyMatchingAnnouncements extends Command
{
    protected $signature = 'announcements:notify-matching';
    protected $description = 'Wysyła powiadomienia o nowych ogłoszeniach pasujących do zapisanych wyszukiwań';

    public function handle()
    {
        $this->info('Sprawdzanie pasujących ogłoszeń...');

        $savedSearches = SavedSearch::where('notify_on_match', true)
            ->with('user')
            ->get();

        $notified = 0;

        foreach ($savedSearches as $savedSearch) {
            $matchingAnnouncements = $savedSearch->findMatchingAnnouncements();

            if ($matchingAnnouncements->count() > 0) {
                try {
                    Mail::to($savedSearch->user->email)->send(
                        new MatchingAnnouncementsMail($savedSearch, $matchingAnnouncements)
                    );

                    $savedSearch->update(['last_notified_at' => now()]);
                    $notified++;

                    $this->info("Wysłano powiadomienie do {$savedSearch->user->email} ({$matchingAnnouncements->count()} ogłoszeń)");
                } catch (\Exception $e) {
                    Log::error("Błąd wysyłania powiadomienia do {$savedSearch->user->email}: " . $e->getMessage());
                    $this->error("Błąd: {$e->getMessage()}");
                }
            }
        }

        $this->info("Wysłano {$notified} powiadomień");
        return 0;
    }
}
