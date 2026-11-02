<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use App\Livewire\Concerns\TracksEvents;
use Livewire\Component;

class ShowMessages extends Component
{
    use TracksEvents;
    public User $otherUser;
    public $messages;
    public $content = '';

    public function mount(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(404);
        }

        $this->otherUser = $user;
        $this->loadMessages();
        $this->markAsRead();
    }

    public function getListeners()
    {
        $ids = [auth()->id(), $this->otherUser->id];
        sort($ids);

        return [
            "echo-private:conversation.{$ids[0]}.{$ids[1]},message.sent" => 'handleIncomingMessage',
        ];
    }

    public function handleIncomingMessage(): void
    {
        $this->loadMessages();
        $this->markAsRead();
        $this->dispatch('scroll-to-bottom');
    }

    public function loadMessages()
    {
        $userId = auth()->id();

        $this->messages = Message::between($userId, $this->otherUser->id)
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

        // Każda wiadomość wysyła e-mail do odbiorcy - bez limitu jedno konto
        // mogło zasypać skrzynkę dowolnego użytkownika (i limit SMTP serwera).
        $rateKey = 'send-message:' . auth()->id();
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $this->addError('content', 'Wysyłasz wiadomości zbyt szybko. Spróbuj ponownie za ' . RateLimiter::availableIn($rateKey) . ' s.');
            return;
        }
        RateLimiter::hit($rateKey, 60);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->otherUser->id,
            'content' => $this->content,
        ]);

        try {
            event(new MessageSent($message->load('sender')));
        } catch (\Exception $e) {
            \Log::warning('Nie udało się wysłać wiadomości na żywo: ' . $e->getMessage());
        }

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

        $this->trackEvent('send_message');
        $this->dispatch('notify', message: 'Wiadomość wysłana!', type: 'success');
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.show-messages')->layout('layouts.app', [
            'title' => 'Chat z ' . $this->otherUser->name . ' - Projekciarz.pl',
        ]);
    }
}

