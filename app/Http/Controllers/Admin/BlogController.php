<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Tag;
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
        $tags = Tag::orderBy('name')->get();
        return view('admin.blog.create', compact('tags'));
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
            'tags' => 'nullable|array',
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
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $metaKeywords,
            'status' => $status,
        ];

        if ($status === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            Log::info('Uploading featured image (store).');
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
            Log::info('Stored featured image path: '.$data['featured_image']);
        } else {
            Log::warning('Store: featured_image not present in request.');
        }

        $post = BlogPost::create($data);

        if (!empty($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Post dodany!');
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $tags = Tag::orderBy('name')->get();
        return view('admin.blog.edit', compact('post', 'tags'));
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
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
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

        $data = [
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $metaKeywords,
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
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

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
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

