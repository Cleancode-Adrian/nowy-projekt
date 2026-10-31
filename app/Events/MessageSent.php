<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(public Message $message)
    {
    }

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        $ids = [$this->message->sender_id, $this->message->receiver_id];
        sort($ids);

        return [
            new PrivateChannel('conversation.' . $ids[0] . '.' . $ids[1]),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'content' => $this->message->content,
            'sender_name' => $this->message->sender->name,
            'created_at' => $this->message->created_at->format('H:i'),
        ];
    }
}
