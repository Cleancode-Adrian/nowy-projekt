<?php

namespace App\Console\Commands;

use App\Models\Proposal;
use App\Models\Announcement;
use App\Mail\InactiveProjectReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RemindInactiveProjects extends Command
{
    protected $signature = 'projects:remind-inactive';
    protected $description = 'Wysyła przypomnienia o nieaktywnych projektach';

    public function handle()
    {
        $this->info('Sprawdzanie nieaktywnych projektów...');

        // Projekty z zaakceptowanymi ofertami, ale bez aktywności przez 7 dni
        $inactiveProposals = Proposal::where('status', 'accepted')
            ->where('accepted_at', '<=', now()->subDays(7))
            ->where('accepted_at', '>', now()->subDays(14)) // Tylko z ostatnich 2 tygodni
            ->with(['announcement.user', 'freelancer'])
            ->get();

        $reminded = 0;

        foreach ($inactiveProposals as $proposal) {
            // Sprawdź czy były jakieś wiadomości w ostatnich 7 dniach między klientem a freelancerem
            $recentMessages = \App\Models\Message::where(function($q) use ($proposal) {
                    $q->where('sender_id', $proposal->user_id)
                      ->where('receiver_id', $proposal->announcement->user_id);
                })
                ->orWhere(function($q) use ($proposal) {
                    $q->where('sender_id', $proposal->announcement->user_id)
                      ->where('receiver_id', $proposal->user_id);
                })
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if (!$recentMessages) {
                // Wyślij przypomnienie do klienta
                try {
                    Mail::to($proposal->announcement->user->email)->send(
                        new InactiveProjectReminderMail($proposal, 'client')
                    );
                    $reminded++;
                } catch (\Exception $e) {
                    Log::error("Błąd wysyłania przypomnienia do klienta: " . $e->getMessage());
                }

                // Wyślij przypomnienie do freelancera
                try {
                    Mail::to($proposal->freelancer->email)->send(
                        new InactiveProjectReminderMail($proposal, 'freelancer')
                    );
                    $reminded++;
                } catch (\Exception $e) {
                    Log::error("Błąd wysyłania przypomnienia do freelancera: " . $e->getMessage());
                }
            }
        }

        $this->info("Wysłano {$reminded} przypomnień");
        return 0;
    }
}
