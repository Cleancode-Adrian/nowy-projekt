<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedAnnouncementController extends Controller
{
    /**
     * Get user's saved announcements
     */
    public function index(Request $request): JsonResponse
    {
        $saved = $request->user()
            ->savedAnnouncements()
            ->with(['category', 'user', 'tags'])
            ->latest('saved_announcements.created_at')
            ->paginate(12);

        return response()->json($saved);
    }

    /**
     * Save (bookmark) an announcement
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
        ]);

        $request->user()->savedAnnouncements()->syncWithoutDetaching([$validated['announcement_id']]);

        return response()->json([
            'message' => 'Ogłoszenie zapisane!',
        ]);
    }

    /**
     * Remove saved announcement
     */
    public function destroy(Request $request, string $announcementId): JsonResponse
    {
        $request->user()->savedAnnouncements()->detach($announcementId);

        return response()->json([
            'message' => 'Ogłoszenie usunięte z zapisanych.',
        ]);
    }

    /**
     * Check if announcement is saved by user
     */
    public function check(Request $request, string $announcementId): JsonResponse
    {
        $isSaved = $request->user()
            ->savedAnnouncements()
            ->where('announcement_id', $announcementId)
            ->exists();

        return response()->json([
            'is_saved' => $isSaved,
        ]);
    }
}

