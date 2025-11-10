<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Proposal;
use Illuminate\Support\Str;
use Livewire\Component;

class AnnouncementProposals extends Component
{
    public Announcement $announcement;
    public $proposals;

    public function mount(Announcement $announcement)
    {
        if ($announcement->user_id !== auth()->id()) {
            abort(403, 'Brak dostępu do tego ogłoszenia');
        }

        $this->announcement = $announcement;
        $this->loadProposals();
    }

    public function loadProposals()
    {
        $this->proposals = $this->announcement->proposals()
            ->with(['freelancer' => function($query) {
                $query->withCount('ratingsReceived')
                      ->withAvg('ratingsReceived', 'rating');
            }])
            ->latest()
            ->get();
    }

    public function accept($proposalId)
    {
        $proposal = Proposal::findOrFail($proposalId);

        if ($proposal->announcement_id !== $this->announcement->id) {
            $this->dispatch('notify', message: 'Błąd: nieprawidłowa propozycja', type: 'error');
            return;
        }

        $proposal->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        Proposal::where('announcement_id', $this->announcement->id)
            ->where('id', '!=', $proposalId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'rejected_at' => now()]);

        // Wysłanie powiadomień
        \App\Models\Notification::createNotification(
            $proposal->user_id,
            'proposal_accepted',
            'Oferta zaakceptowana! ✅',
            'Twoja oferta do "' . $this->announcement->title . '" została zaakceptowana!',
            route('proposals.index')
        );

        // Email notification
        try {
            \Mail::to($proposal->freelancer->email)->send(new \App\Mail\ProposalAcceptedMail($proposal));
        } catch (\Exception $e) {
            \Log::warning('Failed to send email: ' . $e->getMessage());
        }

        $this->loadProposals();
        $this->dispatch('notify', message: 'Oferta zaakceptowana! ✅', type: 'success');
    }

    public function reject($proposalId)
    {
        $proposal = Proposal::findOrFail($proposalId);

        if ($proposal->announcement_id !== $this->announcement->id) {
            $this->dispatch('notify', message: 'Błąd: nieprawidłowa propozycja', type: 'error');
            return;
        }

        $proposal->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        $this->loadProposals();
        $this->dispatch('notify', message: 'Oferta odrzucona', type: 'info');
    }

    public function render()
    {
        return view('livewire.announcement-proposals')->layout('layouts.app', [
            'title' => 'Oferty do ogłoszenia: ' . $this->announcement->title . ' - Projekciarz.pl',
            'description' => 'Zarządzaj otrzymanymi ofertami od freelancerów',
        ]);
    }
}

