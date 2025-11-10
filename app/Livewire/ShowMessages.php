<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;

class ShowMessages extends Component
{
    public User $otherUser;
    public $messages;
    public $content = '';

    public function mount(User $user)
    {
        $this->otherUser = $user;
        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadMessages()
    {
        $userId = auth()->id();

        $this->messages = Message::where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $this->otherUser->id);
            })
            ->orWhere(function($query) use ($userId) {
                $query->where('sender_id', $this->otherUser->id)
                      ->where('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function markAsRead()
    {
        Message::where('sender_id', $this->otherUser->id)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function sendMessage()
    {
        $this->validate([
            'content' => 'required|string|min:1|max:1000',
        ], [
            'content.required' => 'Wiadomość nie może być pusta',
            'content.max' => 'Wiadomość może mieć maksymalnie 1000 znaków',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->otherUser->id,
            'content' => $this->content,
        ]);

        // Wysłanie powiadomień
        \App\Models\Notification::createNotification(
            $this->otherUser->id,
            'new_message',
            'Nowa wiadomość 💬',
            auth()->user()->name . ' wysłał Ci wiadomość',
            route('messages.show', auth()->id())
        );

        // Email notification
        try {
            \Mail::to($this->otherUser->email)->send(new \App\Mail\NewMessageMail($message));
        } catch (\Exception $e) {
            \Log::warning('Failed to send email: ' . $e->getMessage());
        }

        $this->content = '';
        $this->loadMessages();

        $this->dispatch('notify', message: 'Wiadomość wysłana!', type: 'success');
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.show-messages')->layout('layouts.app', [
            'title' => 'Chat z ' . $this->otherUser->name . ' - WebFreelance',
        ]);
    }
}

