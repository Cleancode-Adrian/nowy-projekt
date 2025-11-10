<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'announcement_id',
        'content',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeBetween($query, $userId1, $userId2)
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)->where('receiver_id', $userId1);
        });
    }
}

