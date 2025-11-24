<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('author')->latest()->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $tags = Tag::forBlogs()->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.blog.create', compact('tags', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:10|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required|min:50',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'meta_keywords' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'featured_image_existing' => 'nullable|string|max:255',
            'featured_image_alt' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'new_tags' => 'nullable|string|max:500',
        ], [
            'title.required' => 'Tytuł jest wymagany',
            'title.min' => 'Tytuł musi mieć minimum 10 znaków',
            'title.max' => 'Tytuł może mieć maksymalnie 255 znaków',
            'excerpt.max' => 'Zajawka może mieć maksymalnie 500 znaków',
            'content.required' => 'Treść wpisu jest wymagana',
            'content.min' => 'Treść musi mieć minimum 50 znaków',
            'meta_title.max' => 'Meta tytuł może mieć maksymalnie 60 znaków',
            'meta_description.max' => 'Meta opis może mieć maksymalnie 160 znaków',
            'featured_image.image' => 'Plik musi być obrazem',
            'featured_image.max' => 'Zdjęcie może mieć maksymalnie 2MB',
        ]);

        Log::info('Store request files', ['keys' => array_keys($request->allFiles())]);

        $status = $request->input('status', 'draft');

        $slug = Str::slug($validated['title']);
        $counter = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['title']) . '-' . $counter++;
        }

        // Convert meta_keywords string to array
        $metaKeywords = null;
        if (!empty($validated['meta_keywords'])) {
            $metaKeywords = array_map('trim', explode(',', $validated['meta_keywords']));
        }

        $data = [
            'author_id' => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $metaKeywords,
            'featured_image_alt' => $validated['featured_image_alt'] ?? null,
            'status' => $status,
        ];

        if ($status === 'published') {
            $data['published_at'] = now();
        }

        if ($request->filled('featured_image_existing')) {
            $data['featured_image'] = $request->input('featured_image_existing');
        } elseif ($request->hasFile('featured_image')) {
            Log::info('Uploading featured image (store).');
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
            Log::info('Stored featured image path: '.$data['featured_image']);
        } else {
            Log::warning('Store: featured_image not present in request.');
        }

        // Create new category if provided
        if (!empty($validated['new_category'])) {
            $newCategory = Category::firstOrCreate(
                ['slug' => Str::slug($validated['new_category'])],
                [
                    'name' => $validated['new_category'],
                    'is_active' => true,
                ]
            );
            $data['category_id'] = $newCategory->id;
        }

        $post = BlogPost::create($data);

        // Handle tags - existing and new
        $tagIds = $validated['tags'] ?? [];

        // Create new tags if provided
        if (!empty($validated['new_tags'])) {
            $newTagNames = array_map('trim', explode(',', $validated['new_tags']));
            foreach ($newTagNames as $tagName) {
                if (!empty($tagName)) {
                    $newTag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName), 'type' => 'blog'],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $newTag->id;
                }
            }
        }

        if (!empty($tagIds)) {
            $post->tags()->sync(array_unique($tagIds));
        }

        return redirect()->route('admin.blog.index')->with('success', 'Post dodany!');
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $tags = Tag::forBlogs()->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.blog.edit', compact('post', 'tags', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|min:10|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required|min:50',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'meta_keywords' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'featured_image_existing' => 'nullable|string|max:255',
            'featured_image_alt' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'new_tags' => 'nullable|string|max:500',
        ], [
            'title.required' => 'Tytuł jest wymagany',
            'title.min' => 'Tytuł musi mieć minimum 10 znaków',
            'content.required' => 'Treść wpisu jest wymagana',
            'content.min' => 'Treść musi mieć minimum 50 znaków',
            'meta_title.max' => 'Meta tytuł może mieć maksymalnie 60 znaków',
            'meta_description.max' => 'Meta opis może mieć maksymalnie 160 znaków',
            'featured_image.image' => 'Plik musi być obrazem',
            'featured_image.max' => 'Zdjęcie może mieć maksymalnie 2MB',
        ]);

        Log::info('Update request files', ['keys' => array_keys($request->allFiles())]);

        // Convert meta_keywords string to array
        $metaKeywords = null;
        if (!empty($validated['meta_keywords'])) {
            $metaKeywords = array_map('trim', explode(',', $validated['meta_keywords']));
        }

        // Create new category if provided
        $categoryId = $validated['category_id'] ?? null;
        if (!empty($validated['new_category'])) {
            $newCategory = Category::firstOrCreate(
                ['slug' => Str::slug($validated['new_category'])],
                [
                    'name' => $validated['new_category'],
                    'is_active' => true,
                ]
            );
            $categoryId = $newCategory->id;
        }

        $data = [
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $metaKeywords,
            'category_id' => $categoryId,
            'featured_image_alt' => $validated['featured_image_alt'] ?? null,
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        if ($request->filled('featured_image_existing')) {
            $data['featured_image'] = $request->input('featured_image_existing');
        } elseif ($request->hasFile('featured_image')) {
            Log::info('Uploading featured image (update).');
            if ($post->featured_image) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
            Log::info('Stored featured image path (update): '.$data['featured_image']);
        } else {
            Log::warning('Update: featured_image not present in request.');
        }

        $post->update($data);

        // Handle tags - existing and new
        $tagIds = $validated['tags'] ?? [];

        // Create new tags if provided
        if (!empty($validated['new_tags'])) {
            $newTagNames = array_map('trim', explode(',', $validated['new_tags']));
            foreach ($newTagNames as $tagName) {
                if (!empty($tagName)) {
                    $newTag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName), 'type' => 'blog'],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $newTag->id;
                }
            }
        }

        if (!empty($tagIds)) {
            $post->tags()->sync(array_unique($tagIds));
        } else {
            $post->tags()->sync([]);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Post zaktualizowany!');
    }

    public function delete($id)
    {
        $post = BlogPost::findOrFail($id);

        if ($post->featured_image) {
            \Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Post usunięty!');
    }
}

