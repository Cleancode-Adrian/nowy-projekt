<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with(['user', 'category', 'tags'])
            ->published()
            ->latest();

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(12);
        return response()->json($announcements);
    }

    public function show(string $id): JsonResponse
    {
        $announcement = Announcement::with(['user', 'category', 'tags'])
            ->published()
            ->findOrFail($id);

        $announcement->incrementViews();
        return response()->json(['announcement' => $announcement]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'deadline' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'is_urgent' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        $announcement = $request->user()->announcements()->create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'location' => $validated['location'] ?? null,
            'is_urgent' => $validated['is_urgent'] ?? false,
            'status' => 'pending',
            'is_approved' => false,
        ]);

        if (isset($validated['tags'])) {
            $announcement->tags()->attach($validated['tags']);
        }

        return response()->json([
            'message' => 'Ogłoszenie dodane i oczekuje na akceptację.',
            'announcement' => $announcement->load(['category', 'tags']),
        ], 201);
    }

    /**
     * Get current user's announcements
     */
    public function myAnnouncements(Request $request): JsonResponse
    {
        $announcements = Announcement::where('user_id', $request->user()->id)
            ->with(['category', 'tags'])
            ->latest()
            ->get();

        return response()->json($announcements);
    }

    /**
     * Delete announcement
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);

        // Only owner or admin can delete
        if ($announcement->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Brak uprawnień'], 403);
        }

        $announcement->delete();

        return response()->json(['message' => 'Ogłoszenie usunięte']);
    }
}

