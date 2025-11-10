<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->withCount(['activeAnnouncements'])
            ->get();

        return response()->json(['categories' => $categories]);
    }

    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->withCount(['activeAnnouncements'])
            ->firstOrFail();

        return response()->json(['category' => $category]);
    }
}

