<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Announcement;
use App\Mail\WeeklyNewsletterMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklyNewsletter extends Command
{
    protected $signature = 'newsletter:send-weekly';
    protected $description = 'Wysyła cotygodniowy newsletter z najlepszymi ofertami do freelancerów';

    public function handle()
    {
        $this->info('Przygotowywanie newslettera...');

        // Najlepsze oferty z ostatniego tygodnia
        $topAnnouncements = Announcement::published()
            ->where('created_at', '>=', now()->subWeek())
            ->with(['user', 'category', 'tags'])
            ->orderBy('proposals_count', 'asc') // Najmniej ofert = więcej szans
            ->orderBy('is_urgent', 'desc')
            ->orderBy('budget_max', 'desc')
            ->take(10)
            ->get();

        if ($topAnnouncements->count() === 0) {
            $this->info('Brak nowych ogłoszeń w tym tygodniu');
            return 0;
        }

        $freelancers = User::where('role', 'freelancer')
            ->where('is_approved', true)
            ->whereNotNull('email_verified_at')
            ->get();

        $sent = 0;

        foreach ($freelancers as $freelancer) {
            try {
                Mail::to($freelancer->email)->send(
                    new WeeklyNewsletterMail($topAnnouncements)
                );
                $sent++;
            } catch (\Exception $e) {
                Log::error("Błąd wysyłania newslettera do {$freelancer->email}: " . $e->getMessage());
            }
        }

        $this->info("Wysłano newsletter do {$sent} freelancerów");
        return 0;
    }
}
