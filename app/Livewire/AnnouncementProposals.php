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
        // Sprawdź czy użytkownik jest zalogowany
        if (!auth()->check()) {
            abort(403, 'Musisz być zalogowany, aby zobaczyć oferty');
        }

        // Właściciel ogłoszenia lub administrator może zobaczyć oferty
        $isOwner = $announcement->user_id === auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        if (!$isOwner && !$isAdmin) {
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

        // Sprawdź uprawnienia - tylko właściciel może akceptować
        if ($proposal->announcement_id !== $this->announcement->id || $this->announcement->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Tylko właściciel ogłoszenia może akceptować oferty', type: 'error');
            return;
        }

        if ($proposal->status !== 'pending') {
            $this->dispatch('notify', message: 'Oferta już została rozpatrzona', type: 'error');
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

        // Sprawdź uprawnienia - tylko właściciel może odrzucać
        if ($proposal->announcement_id !== $this->announcement->id || $this->announcement->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Tylko właściciel ogłoszenia może odrzucać oferty', type: 'error');
            return;
        }

        if ($proposal->status !== 'pending') {
            $this->dispatch('notify', message: 'Oferta już została rozpatrzona', type: 'error');
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

