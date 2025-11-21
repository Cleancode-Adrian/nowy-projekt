<?php

namespace App\Livewire;

use App\Models\Proposal;
use Livewire\Component;

class ProposalsList extends Component
{
    public $announcementId;
    public $proposals;

    public function mount()
    {
        $this->loadProposals();
    }

    public function loadProposals()
    {
        $this->proposals = Proposal::where('announcement_id', $this->announcementId)
            ->with(['freelancer', 'announcement'])
            ->latest()
            ->get();
    }

    public function accept($proposalId)
    {
        $proposal = Proposal::findOrFail($proposalId);

        // Tylko właściciel ogłoszenia może akceptować oferty (nie administrator)
        if ($proposal->announcement->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
            return;
        }

        $proposal->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Odrzuć pozostałe
        Proposal::where('announcement_id', $this->announcementId)
            ->where('id', '!=', $proposalId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'rejected_at' => now()]);

        $this->loadProposals();
        $this->dispatch('notify', message: 'Oferta zaakceptowana!', type: 'success');
    }

    public function reject($proposalId)
    {
        $proposal = Proposal::findOrFail($proposalId);

        // Tylko właściciel ogłoszenia może odrzucać oferty (nie administrator)
        if ($proposal->announcement->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
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
        return view('livewire.proposals-list');
    }
}

