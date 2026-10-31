<?php

namespace App\Livewire;

use App\Models\Contract;
use App\Models\Notification;
use Livewire\Component;

class ContractShow extends Component
{
    public Contract $contract;

    public $newTitle = '';
    public $newDescription = '';
    public $newDueDate = '';

    public function mount(Contract $contract)
    {
        if (!auth()->check() || (!$contract->isParticipant(auth()->id()) && !auth()->user()->isAdmin())) {
            abort(403, 'Brak dostępu do tego kontraktu');
        }

        $this->contract = $contract;
        $this->loadContract();
    }

    public function loadContract()
    {
        $this->contract = Contract::with(['announcement', 'client', 'freelancer', 'milestones'])
            ->findOrFail($this->contract->id);
    }

    private function isClient(): bool
    {
        return $this->contract->client_id === auth()->id();
    }

    private function isFreelancer(): bool
    {
        return $this->contract->freelancer_id === auth()->id();
    }

    private function otherPartyId(): int
    {
        return $this->isClient() ? $this->contract->freelancer_id : $this->contract->client_id;
    }

    public function addMilestone()
    {
        // Admin może podglądać kontrakt, ale etapy dodają tylko strony umowy.
        if (!$this->isClient() && !$this->isFreelancer()) {
            return;
        }

        if ($this->contract->status !== 'active') {
            $this->dispatch('notify', message: 'Kontrakt jest zakończony', type: 'error');
            return;
        }

        $this->validate([
            'newTitle' => 'required|string|min:3|max:255',
            'newDescription' => 'nullable|string|max:2000',
            'newDueDate' => 'nullable|date',
        ]);

        $position = $this->contract->milestones->max('position') + 1;

        $this->contract->milestones()->create([
            'title' => $this->newTitle,
            'description' => $this->newDescription ?: null,
            'due_date' => $this->newDueDate ?: null,
            'position' => $position,
        ]);

        Notification::createNotification(
            $this->otherPartyId(),
            'milestone_added',
            'Nowy kamień milowy',
            'Dodano nowy etap "' . $this->newTitle . '" w kontrakcie "' . $this->contract->announcement->title . '".',
            route('contracts.show', $this->contract->id)
        );

        $this->reset(['newTitle', 'newDescription', 'newDueDate']);
        $this->loadContract();
        $this->dispatch('notify', message: 'Kamień milowy dodany', type: 'success');
    }

    public function startMilestone($milestoneId)
    {
        $milestone = $this->contract->milestones->firstWhere('id', $milestoneId);

        if (!$milestone || !$this->isFreelancer() || $milestone->status !== 'pending') {
            return;
        }

        $milestone->update(['status' => 'in_progress']);
        $this->loadContract();
    }

    public function submitMilestone($milestoneId)
    {
        $milestone = $this->contract->milestones->firstWhere('id', $milestoneId);

        if (!$milestone || !$this->isFreelancer() || !in_array($milestone->status, ['pending', 'in_progress'])) {
            return;
        }

        $milestone->update(['status' => 'submitted', 'submitted_at' => now()]);

        Notification::createNotification(
            $this->contract->client_id,
            'milestone_submitted',
            'Etap zgłoszony do odbioru',
            'Wykonawca zgłosił etap "' . $milestone->title . '" jako gotowy do odbioru.',
            route('contracts.show', $this->contract->id)
        );

        $this->loadContract();
        $this->dispatch('notify', message: 'Etap zgłoszony do odbioru', type: 'success');
    }

    public function approveMilestone($milestoneId)
    {
        $milestone = $this->contract->milestones->firstWhere('id', $milestoneId);

        if (!$milestone || !$this->isClient() || $milestone->status !== 'submitted') {
            return;
        }

        $milestone->update(['status' => 'approved', 'approved_at' => now()]);

        Notification::createNotification(
            $this->contract->freelancer_id,
            'milestone_approved',
            'Etap zatwierdzony ✅',
            'Klient zatwierdził etap "' . $milestone->title . '".',
            route('contracts.show', $this->contract->id)
        );

        $this->contract->refreshStatus();
        $this->loadContract();
        $this->dispatch('notify', message: 'Etap zatwierdzony', type: 'success');
    }

    public function reopenMilestone($milestoneId)
    {
        $milestone = $this->contract->milestones->firstWhere('id', $milestoneId);

        if (!$milestone || !$this->isClient() || $milestone->status !== 'submitted') {
            return;
        }

        $milestone->update(['status' => 'in_progress', 'submitted_at' => null]);

        Notification::createNotification(
            $this->contract->freelancer_id,
            'milestone_reopened',
            'Etap wymaga poprawek',
            'Klient odesłał etap "' . $milestone->title . '" do poprawy.',
            route('contracts.show', $this->contract->id)
        );

        $this->loadContract();
        $this->dispatch('notify', message: 'Etap odesłany do poprawy', type: 'info');
    }

    public function deleteMilestone($milestoneId)
    {
        $milestone = $this->contract->milestones->firstWhere('id', $milestoneId);

        if (!$milestone || !$this->isClient() || $milestone->status !== 'pending') {
            return;
        }

        $milestone->delete();
        $this->loadContract();
        $this->dispatch('notify', message: 'Kamień milowy usunięty', type: 'info');
    }

    public function cancelContract()
    {
        if (!$this->isClient() || $this->contract->status !== 'active') {
            return;
        }

        $this->contract->update(['status' => 'cancelled']);

        Notification::createNotification(
            $this->contract->freelancer_id,
            'contract_cancelled',
            'Kontrakt anulowany',
            'Klient anulował kontrakt dla ogłoszenia "' . $this->contract->announcement->title . '".',
            route('contracts.show', $this->contract->id)
        );

        $this->loadContract();
        $this->dispatch('notify', message: 'Kontrakt anulowany', type: 'info');
    }

    public function render()
    {
        return view('livewire.contract-show')->layout('layouts.app', [
            'title' => 'Kontrakt: ' . $this->contract->announcement->title . ' - Projekciarz.pl',
        ]);
    }
}
