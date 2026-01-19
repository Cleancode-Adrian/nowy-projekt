<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\WeeklySummaryMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklySummary extends Command
{
    protected $signature = 'users:send-weekly-summary';
    protected $description = 'Wysyła cotygodniowe podsumowanie aktywności użytkownikom';

    public function handle()
    {
        $this->info('Przygotowywanie cotygodniowych podsumowań...');

        $users = User::whereNotNull('email_verified_at')
            ->where('is_approved', true)
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            $summary = $this->getUserSummary($user);

            // Wyślij tylko jeśli użytkownik ma jakąś aktywność
            if ($summary['has_activity']) {
                try {
                    Mail::to($user->email)->send(
                        new WeeklySummaryMail($user, $summary)
                    );
                    $sent++;
                } catch (\Exception $e) {
                    Log::error("Błąd wysyłania podsumowania do {$user->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("Wysłano {$sent} podsumowań");
        return 0;
    }

    private function getUserSummary(User $user): array
    {
        $weekAgo = now()->subWeek();

        if ($user->isFreelancer()) {
            $newProposals = $user->proposals()
                ->where('created_at', '>=', $weekAgo)
                ->count();

            $acceptedProposals = $user->proposals()
                ->where('status', 'accepted')
                ->where('accepted_at', '>=', $weekAgo)
                ->count();

            $newMessages = $user->receivedMessages()
                ->where('created_at', '>=', $weekAgo)
                ->count();

            return [
                'has_activity' => $newProposals > 0 || $acceptedProposals > 0 || $newMessages > 0,
                'new_proposals' => $newProposals,
                'accepted_proposals' => $acceptedProposals,
                'new_messages' => $newMessages,
                'profile_views' => $user->profile_views,
            ];
        } else {
            $newAnnouncements = $user->announcements()
                ->where('created_at', '>=', $weekAgo)
                ->count();

            $newProposals = $user->announcements()
                ->withCount(['proposals' => fn($q) => $q->where('created_at', '>=', $weekAgo)])
                ->get()
                ->sum('proposals_count');

            $newMessages = $user->receivedMessages()
                ->where('created_at', '>=', $weekAgo)
                ->count();

            return [
                'has_activity' => $newAnnouncements > 0 || $newProposals > 0 || $newMessages > 0,
                'new_announcements' => $newAnnouncements,
                'new_proposals' => $newProposals,
                'new_messages' => $newMessages,
            ];
        }
    }
}
