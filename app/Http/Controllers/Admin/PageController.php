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
        $parentPages = Page::whereNull('parent_id')->orderBy('title')->get();
        return view('admin.pages.create', compact('parentPages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'route_name' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:500',
            'content' => 'nullable|min:10',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'parent_id' => 'nullable|exists:pages,id',
            'order' => 'nullable|integer|min:0',
            'show_in_menu' => 'boolean',
            'menu_position' => 'nullable|in:footer,header,both',
            'menu_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:50',
        ], [
            'title.required' => 'Tytuł jest wymagany',
            'title.min' => 'Tytuł musi mieć minimum 3 znaki',
            'title.max' => 'Tytuł może mieć maksymalnie 255 znaków',
            'slug.unique' => 'Ten slug jest już zajęty',
            'content.min' => 'Treść musi mieć minimum 10 znaków (lub użyj route_name/external_url)',
            'meta_title.max' => 'Meta tytuł może mieć maksymalnie 60 znaków',
            'meta_description.max' => 'Meta opis może mieć maksymalnie 160 znaków',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        $counter = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['title']) . '-' . $counter++;
        }

        // Walidacja: musi być content LUB route_name LUB external_url
        if (empty($validated['content']) && empty($validated['route_name']) && empty($validated['external_url'])) {
            return back()->withErrors(['content' => 'Musisz podać treść strony, route_name lub external_url'])->withInput();
        }

        $data = [
            'title' => $validated['title'],
            'slug' => $slug,
            'route_name' => $validated['route_name'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'content' => $validated['content'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->has('is_active'),
            'is_system' => $request->has('is_system'),
            'parent_id' => $validated['parent_id'] ?? null,
            'order' => $validated['order'] ?? 0,
            'show_in_menu' => $request->has('show_in_menu'),
            'menu_position' => $validated['menu_position'] ?? null,
            'menu_order' => $validated['menu_order'] ?? 0,
            'icon' => $validated['icon'] ?? null,
        ];

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Strona dodana!');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $parentPages = Page::whereNull('parent_id')->where('id', '!=', $id)->orderBy('title')->get();
        return view('admin.pages.edit', compact('page', 'parentPages'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $id,
            'route_name' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:500',
            'content' => 'nullable|min:10',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'parent_id' => 'nullable|exists:pages,id',
            'order' => 'nullable|integer|min:0',
            'show_in_menu' => 'boolean',
            'menu_position' => 'nullable|in:footer,header,both',
            'menu_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:50',
        ], [
            'title.required' => 'Tytuł jest wymagany',
            'title.min' => 'Tytuł musi mieć minimum 3 znaki',
            'slug.unique' => 'Ten slug jest już zajęty',
            'content.min' => 'Treść musi mieć minimum 10 znaków (lub użyj route_name/external_url)',
            'meta_title.max' => 'Meta tytuł może mieć maksymalnie 60 znaków',
            'meta_description.max' => 'Meta opis może mieć maksymalnie 160 znaków',
        ]);

        // Walidacja: musi być content LUB route_name LUB external_url
        if (empty($validated['content']) && empty($validated['route_name']) && empty($validated['external_url'])) {
            return back()->withErrors(['content' => 'Musisz podać treść strony, route_name lub external_url'])->withInput();
        }

        // Zapobiegaj cyklicznym referencjom (strona nie może być swoim rodzicem)
        if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
            return back()->withErrors(['parent_id' => 'Strona nie może być swoim rodzicem'])->withInput();
        }

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
            'route_name' => $validated['route_name'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'content' => $validated['content'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_active' => $request->has('is_active'),
            'is_system' => $request->has('is_system'),
            'parent_id' => $validated['parent_id'] ?? null,
            'order' => $validated['order'] ?? 0,
            'show_in_menu' => $request->has('show_in_menu'),
            'menu_position' => $validated['menu_position'] ?? null,
            'menu_order' => $validated['menu_order'] ?? 0,
            'icon' => $validated['icon'] ?? null,
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

