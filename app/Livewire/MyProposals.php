<?php

namespace App\Livewire;

use App\Models\Proposal;
use Livewire\Component;

class MyProposals extends Component
{
    public $proposals;
    public $stats;

    public function mount()
    {
        $this->loadProposals();
    }

    public function loadProposals()
    {
        $this->proposals = Proposal::where('user_id', auth()->id())
            ->with(['announcement' => function($query) {
                $query->with('user', 'category');
            }])
            ->latest()
            ->get();

        $this->stats = [
            'total' => $this->proposals->count(),
            'pending' => $this->proposals->where('status', 'pending')->count(),
            'accepted' => $this->proposals->where('status', 'accepted')->count(),
            'rejected' => $this->proposals->where('status', 'rejected')->count(),
        ];
    }

    public function withdraw($proposalId)
    {
        $proposal = Proposal::findOrFail($proposalId);

        if ($proposal->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
            return;
        }

        if ($proposal->status !== 'pending') {
            $this->dispatch('notify', message: 'Możesz wycofać tylko oczekujące oferty', type: 'error');
            return;
        }

        $proposal->update(['status' => 'withdrawn']);

        $this->loadProposals();
        $this->dispatch('notify', message: 'Oferta wycofana', type: 'info');
    }

    public function render()
    {
        return view('livewire.my-proposals')->layout('layouts.app', [
            'title' => 'Moje oferty - Projekciarz.pl',
            'description' => 'Lista wszystkich wysłanych ofert',
        ]);
    }
}

