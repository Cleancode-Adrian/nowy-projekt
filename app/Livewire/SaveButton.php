<?php

namespace App\Livewire;

use Livewire\Component;

class SaveButton extends Component
{
    public $announcementId;
    public $full = false; // full width button or icon only
    public $isSaved = false;

    public function mount()
    {
        $this->checkIfSaved();
    }

    public function checkIfSaved()
    {
        if (auth()->check()) {
            $this->isSaved = auth()->user()
                ->savedAnnouncements()
                ->where('announcement_id', $this->announcementId)
                ->exists();
        }
    }

    public function toggle()
    {
        if (!auth()->check()) {
            $this->dispatch('notify', message: 'Zaloguj się aby zapisać ogłoszenie', type: 'error');
            return;
        }

        if ($this->isSaved) {
            auth()->user()->savedAnnouncements()->detach($this->announcementId);
            $this->isSaved = false;
            $this->dispatch('notify', message: 'Usunięto z zapisanych');
        } else {
            auth()->user()->savedAnnouncements()->attach($this->announcementId);
            $this->isSaved = true;
            $this->dispatch('notify', message: 'Dodano do zapisanych');
        }
    }

    public function render()
    {
        return view('livewire.save-button');
    }
}

