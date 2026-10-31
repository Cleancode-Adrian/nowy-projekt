<?php

namespace App\Livewire;

use App\Models\Announcement;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SaveButton extends Component
{
    #[Locked]
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

        // Stan liczymy z bazy, nie z $isSaved przysłanego przez klienta.
        $alreadySaved = auth()->user()
            ->savedAnnouncements()
            ->where('announcement_id', $this->announcementId)
            ->exists();

        if ($alreadySaved) {
            auth()->user()->savedAnnouncements()->detach($this->announcementId);
            $this->isSaved = false;
            $this->dispatch('notify', message: 'Usunięto z zapisanych');
        } else {
            if (!Announcement::published()->whereKey($this->announcementId)->exists()) {
                $this->dispatch('notify', message: 'Ogłoszenie jest niedostępne', type: 'error');
                return;
            }

            auth()->user()->savedAnnouncements()->syncWithoutDetaching([$this->announcementId]);
            $this->isSaved = true;
            $this->dispatch('notify', message: 'Dodano do zapisanych');
        }
    }

    public function render()
    {
        return view('livewire.save-button');
    }
}
