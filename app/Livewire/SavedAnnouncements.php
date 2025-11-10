<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class SavedAnnouncements extends Component
{
    use WithPagination;

    public function unsave($announcementId)
    {
        auth()->user()->savedAnnouncements()->detach($announcementId);
        
        $this->dispatch('notify', 
            message: 'Usunięto z zapisanych!', 
            type: 'success'
        );
    }

    public function render()
    {
        $savedAnnouncements = auth()->user()
            ->savedAnnouncements()
            ->with(['category', 'user', 'tags'])
            ->published()
            ->latest('saved_announcements.created_at')
            ->paginate(12);

        return view('livewire.saved-announcements', [
            'savedAnnouncements' => $savedAnnouncements,
        ])->layout('layouts.app', [
            'title' => 'Zapisane ogłoszenia - Projekciarz.pl',
            'description' => 'Twoje zapisane projekty i zlecenia',
        ]);
    }
}
