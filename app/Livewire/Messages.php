<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Messages extends Component
{
    public $conversations = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $userId = auth()->id();

        $this->conversations = Message::select('messages.*')
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function($message) use ($userId) {
                return $message->sender_id == $userId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function($messages, $otherUserId) use ($userId) {
                $latestMessage = $messages->first();
                $otherUser = $latestMessage->sender_id == $userId
                    ? $latestMessage->receiver
                    : $latestMessage->sender;

                $unreadCount = $messages->where('receiver_id', $userId)
                                       ->where('is_read', false)
                                       ->count();

                return [
                    'user' => $otherUser,
                    'latest_message' => $latestMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc(function($conversation) {
                return $conversation['latest_message']->created_at;
            })
            ->values();

        $this->unreadCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function render()
    {
        return view('livewire.messages')->layout('layouts.app', [
            'title' => 'Wiadomości - WebFreelance',
            'description' => 'Twoje prywatne rozmowy',
        ]);
    }
}

