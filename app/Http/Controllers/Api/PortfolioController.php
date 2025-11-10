<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Get portfolio items for a user
     */
    public function index($userId): JsonResponse
    {
        $items = PortfolioItem::where('user_id', $userId)
            ->ordered()
            ->get();

        return response()->json($items);
    }

    /**
     * Get my portfolio items
     */
    public function myPortfolio(Request $request): JsonResponse
    {
        $items = PortfolioItem::where('user_id', $request->user()->id)
            ->ordered()
            ->get();

        return response()->json($items);
    }

    /**
     * Create portfolio item
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120', // 5MB
            'images.*' => 'nullable|image|max:5120',
            'url' => 'nullable|url',
            'technologies' => 'nullable|array',
            'completed_at' => 'nullable|date',
            'is_featured' => 'boolean',
        ]);

        $data = $validated;
        $data['user_id'] = $request->user()->id;

        // Handle main image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('portfolio', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('portfolio', 'public');
            }
            $data['images'] = $images;
        }

        $item = PortfolioItem::create($data);

        return response()->json([
            'message' => 'Pozycja portfolio dodana!',
            'item' => $item,
        ], 201);
    }

    /**
     * Update portfolio item
     */
    public function update(Request $request, $id): JsonResponse
    {
        $item = PortfolioItem::findOrFail($id);

        if ($item->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
            'url' => 'nullable|url',
            'technologies' => 'nullable|array',
            'completed_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        // Handle main image
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('portfolio', 'public');
        }

        $item->update($validated);

        return response()->json([
            'message' => 'Portfolio zaktualizowane!',
            'item' => $item,
        ]);
    }

    /**
     * Delete portfolio item
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $item = PortfolioItem::findOrFail($id);

        if ($item->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete images
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        if ($item->images) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $item->delete();

        return response()->json(['message' => 'Pozycja usunięta']);
    }
}

