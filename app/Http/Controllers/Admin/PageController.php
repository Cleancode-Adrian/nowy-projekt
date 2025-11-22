<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::ordered()->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required|min:10',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'show_in_menu' => 'boolean',
            'menu_position' => 'nullable|in:footer,header,both',
            'menu_order' => 'nullable|integer|min:0',
        ], [
            'title.required' => 'Tytuł jest wymagany',
            'title.min' => 'Tytuł musi mieć minimum 3 znaki',
            'title.max' => 'Tytuł może mieć maksymalnie 255 znaków',
            'slug.unique' => 'Ten slug jest już zajęty',
            'content.required' => 'Treść strony jest wymagana',
            'content.min' => 'Treść musi mieć minimum 10 znaków',
            'meta_title.max' => 'Meta tytuł może mieć maksymalnie 60 znaków',
            'meta_description.max' => 'Meta opis może mieć maksymalnie 160 znaków',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        $counter = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['title']) . '-' . $counter++;
        }

        $data = [
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'is_active' => $request->has('is_active'),
            'is_system' => $request->has('is_system'),
            'order' => $validated['order'] ?? 0,
        ];

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Strona dodana!');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $id,
            'content' => 'required|min:10',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'show_in_menu' => 'boolean',
            'menu_position' => 'nullable|in:footer,header,both',
            'menu_order' => 'nullable|integer|min:0',
        ], [
            'title.required' => 'Tytuł jest wymagany',
            'title.min' => 'Tytuł musi mieć minimum 3 znaki',
            'slug.unique' => 'Ten slug jest już zajęty',
            'content.required' => 'Treść strony jest wymagana',
            'content.min' => 'Treść musi mieć minimum 10 znaków',
            'meta_title.max' => 'Meta tytuł może mieć maksymalnie 60 znaków',
            'meta_description.max' => 'Meta opis może mieć maksymalnie 160 znaków',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        if ($slug !== $page->slug && Page::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $counter = 1;
            $originalSlug = $slug;
            while (Page::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
        }

        $data = [
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'is_active' => $request->has('is_active'),
            'is_system' => $request->has('is_system'),
            'order' => $validated['order'] ?? 0,
            'show_in_menu' => $request->has('show_in_menu'),
            'menu_position' => $validated['menu_position'] ?? null,
            'menu_order' => $validated['menu_order'] ?? 0,
        ];

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Strona zaktualizowana!');
    }

    public function delete($id)
    {
        $page = Page::findOrFail($id);

        // Nie pozwól usunąć stron systemowych
        if ($page->is_system) {
            return redirect()->route('admin.pages.index')->with('error', 'Nie można usunąć strony systemowej!');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Strona usunięta!');
    }
}

