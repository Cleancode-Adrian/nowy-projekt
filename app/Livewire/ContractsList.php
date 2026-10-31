<?php

namespace App\Livewire;

use App\Models\Contract;
use Livewire\Component;
use Livewire\WithPagination;

class ContractsList extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public function render()
    {
        $userId = auth()->id();

        $contracts = Contract::with(['announcement', 'client', 'freelancer', 'milestones'])
            ->where(function ($q) use ($userId) {
                $q->where('client_id', $userId)->orWhere('freelancer_id', $userId);
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.contracts-list', compact('contracts'))
            ->layout('layouts.app', [
                'title' => 'Moje kontrakty - Projekciarz.pl',
            ]);
    }
}
