<?php

namespace App\Livewire;

use Livewire\Component;

class Notifications extends Component
{
    public $notifications;
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()->notifications()->latest()->limit(20)->get();
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['is_read' => true, 'read_at' => now()]);
        $this->loadNotifications();
        $this->dispatch('notify', message: 'Wszystkie powiadomienia oznaczone jako przeczytane', type: 'success');
    }

    public function render()
    {
        return view('livewire.notifications')->layout('layouts.app', [
            'title' => 'Powiadomienia - WebFreelance',
        ]);
    }
}

