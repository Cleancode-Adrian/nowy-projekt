<?php

namespace App\Livewire;

use App\Models\Announcement;
use Livewire\Component;

class Dashboard extends Component
{
    public $announcements;
    public $stats;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->announcements = Announcement::where('user_id', auth()->id())
            ->with(['category', 'tags'])
            ->withCount('proposals')
            ->latest()
            ->get();

        $this->stats = [
            'total' => $this->announcements->count(),
            'pending' => $this->announcements->where('is_approved', false)->where('status', 'pending')->count(),
            'approved' => $this->announcements->where('is_approved', true)->where('status', 'published')->count(),
            'rejected' => $this->announcements->where('status', 'rejected')->count(),
        ];
    }

    public function delete($id)
    {
        $announcement = Announcement::findOrFail($id);

        if ($announcement->user_id !== auth()->id()) {
            $this->dispatch('notify', message: 'Brak uprawnień', type: 'error');
            return;
        }

        $announcement->delete();
        $this->loadData();
        $this->dispatch('notify', message: 'Ogłoszenie usunięte', type: 'success');
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', [
            'title' => 'Panel użytkownika - WebFreelance',
        ]);
    }
}

