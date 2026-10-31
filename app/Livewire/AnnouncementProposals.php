<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Contract;
use App\Models\ContractMilestone;
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
            }, 'contract'])
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

        $contract = Contract::create([
            'announcement_id' => $this->announcement->id,
            'proposal_id' => $proposal->id,
            'client_id' => $this->announcement->user_id,
            'freelancer_id' => $proposal->user_id,
            'status' => 'active',
        ]);

        $contract->milestones()->create([
            'title' => 'Realizacja zlecenia: ' . Str::limit($this->announcement->title, 80),
            'description' => $proposal->description,
            'position' => 1,
            'due_date' => $proposal->delivery_days ? now()->addDays($proposal->delivery_days) : null,
        ]);

        // Wysłanie powiadomień
        \App\Models\Notification::createNotification(
            $proposal->user_id,
            'proposal_accepted',
            'Oferta zaakceptowana! ✅',
            'Twoja oferta do "' . $this->announcement->title . '" została zaakceptowana! Możesz teraz zarządzać kamieniami milowymi.',
            route('contracts.show', $contract->id)
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

