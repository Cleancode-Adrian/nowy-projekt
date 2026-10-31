<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Proposal;
use Livewire\Component;

class ProposalForm extends Component
{
    public Announcement $announcement;
    public $price = '';
    public $delivery_days = '';
    public $description = '';
    public $hasProposal = false;

    public function mount()
    {
        $this->checkExistingProposal();
    }

    public function checkExistingProposal()
    {
        if (auth()->check()) {
            $this->hasProposal = Proposal::where('announcement_id', $this->announcement->id)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();
        }
    }

    public function submit()
    {
        if (!auth()->check()) {
            $this->dispatch('notify', message: 'Zaloguj się, aby złożyć ofertę', type: 'error');
            return;
        }

        if (auth()->id() === $this->announcement->user_id) {
            $this->dispatch('notify', message: 'Nie możesz złożyć oferty do własnego ogłoszenia', type: 'error');
            return;
        }

        // Oferty tylko do opublikowanych, otwartych ogłoszeń - stan bierzemy z bazy.
        $this->announcement->refresh();
        if (!$this->announcement->is_approved || $this->announcement->status !== 'published') {
            $this->dispatch('notify', message: 'To ogłoszenie nie przyjmuje już ofert', type: 'error');
            return;
        }

        // $hasProposal pochodzi od klienta - duplikat sprawdzamy w bazie.
        $this->checkExistingProposal();
        if ($this->hasProposal) {
            $this->dispatch('notify', message: 'Już złożyłeś ofertę do tego ogłoszenia', type: 'error');
            return;
        }

        $validated = $this->validate([
            'price' => 'required|numeric|min:0',
            'delivery_days' => 'required|integer|min:1|max:365',
            'description' => 'required|string|min:50|max:2000',
        ], [
            'price.required' => 'Podaj cenę oferty',
            'price.min' => 'Cena musi być większa niż 0',
            'delivery_days.required' => 'Podaj termin realizacji',
            'delivery_days.min' => 'Termin musi być co najmniej 1 dzień',
            'description.required' => 'Opisz swoją ofertę',
            'description.min' => 'Opis musi mieć minimum 50 znaków',
        ]);

        $proposal = Proposal::create([
            'announcement_id' => $this->announcement->id,
            'user_id' => auth()->id(),
            'price' => $validated['price'],
            'delivery_days' => $validated['delivery_days'],
            'description' => $validated['description'],
        ]);

        $this->announcement->increment('proposals_count');

        // Wysłanie powiadomień
        \App\Models\Notification::createNotification(
            $this->announcement->user_id,
            'new_proposal',
            'Nowa oferta! 📨',
            auth()->user()->name . ' wysłał ofertę do "' . $this->announcement->title . '"',
            route('announcements.proposals', $this->announcement)
        );

        // Email notification
        try {
            \Mail::to($this->announcement->user->email)->send(new \App\Mail\NewProposalMail($proposal));
        } catch (\Exception $e) {
            \Log::warning('Failed to send email: ' . $e->getMessage());
        }

        $this->hasProposal = true;
        $this->reset(['price', 'delivery_days', 'description']);

        $this->dispatch('notify', message: 'Oferta złożona pomyślnie!', type: 'success');
    }

    public function render()
    {
        return view('livewire.proposal-form');
    }
}

