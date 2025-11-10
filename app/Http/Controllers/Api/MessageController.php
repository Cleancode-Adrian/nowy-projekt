<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Get conversations list
     */
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Message::select('messages.*')
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function($message) use ($userId) {
                return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function($messages) use ($userId) {
                $latest = $messages->first();
                $other = $latest->sender_id === $userId ? $latest->receiver : $latest->sender;
                $unreadCount = $messages->where('receiver_id', $userId)->where('is_read', false)->count();

                return [
                    'user' => $other,
                    'last_message' => $latest,
                    'unread_count' => $unreadCount,
                ];
            })
            ->values();

        return response()->json($conversations);
    }

    /**
     * Get messages with specific user
     */
    public function show(Request $request, $userId): JsonResponse
    {
        $currentUserId = $request->user()->id;

        $messages = Message::between($currentUserId, $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('receiver_id', $currentUserId)
            ->where('sender_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $otherUser = User::findOrFail($userId);

        return response()->json([
            'messages' => $messages,
            'other_user' => $otherUser,
        ]);
    }

    /**
     * Send a message
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:5000',
            'announcement_id' => 'nullable|exists:announcements,id',
        ]);

        if ($validated['receiver_id'] == $request->user()->id) {
            return response()->json(['message' => 'Nie możesz wysłać wiadomości do siebie'], 422);
        }

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $validated['receiver_id'],
            'announcement_id' => $validated['announcement_id'] ?? null,
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Wiadomość wysłana',
            'data' => $message->load(['sender', 'receiver']),
        ], 201);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        if ($message->receiver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json(['message' => 'Wiadomość oznaczona jako przeczytana']);
    }

    /**
     * Get unread count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}

