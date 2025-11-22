@extends('admin.layout')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            📄
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edytuj stronę</h1>
            <p class="text-gray-600">Modyfikuj treść strony statycznej</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.pages.update', $page->id) }}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Title --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-heading text-blue-600"></i>
                    Tytuł strony *
                </label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}"
                       class="w-full px-4 py-3 text-lg border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                       placeholder="np. O nas">
                @error('title') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Slug --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-link text-purple-600"></i>
                    Slug (URL) - opcjonalnie
                </label>
                <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('slug') border-red-500 @enderror"
                       placeholder="np. o-nas">
                @error('slug') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-2">💡 Zmiana slugu może wpłynąć na linki do strony</p>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-green-600"></i>
                    Treść strony *
                </label>
                <div id="editor-container" style="height: 500px;" class="bg-white border-2 border-gray-200 rounded-lg @error('content') border-red-500 @enderror"></div>
                <textarea id="content" name="content" class="hidden">{{ old('content', $page->content) }}</textarea>
                @error('content') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-2">💡 Użyj narzędzi edytora do formatowania tekstu</p>
            </div>

            {{-- SEO Section --}}
            <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-sm border-2 border-green-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-green-600"></i>
                    Optymalizacja SEO
                </h3>
                <div class="space-y-4">
                    {{-- Meta Title --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-tag text-blue-600"></i>
                            Meta Tytuł (opcjonalnie)
                        </label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="np. O nas - Projekciarz.pl"
                               maxlength="60">
                        @error('meta_title') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-2">💡 Wyświetla się w Google (maks. 60 znaków)</p>
                    </div>

                    {{-- Meta Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-purple-600"></i>
                            Meta Opis (opcjonalnie)
                        </label>
                        <textarea name="meta_description" rows="3"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Opis wyświetlany w wynikach wyszukiwania Google..."
                                  maxlength="160">{{ old('meta_description', $page->meta_description) }}</textarea>
                        @error('meta_description') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-2">💡 Wyświetla się w Google (maks. 160 znaków)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-cog text-gray-600"></i>
                    Ustawienia
                </h3>
                <div class="space-y-4">
                    {{-- Is Active --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Strona aktywna</span>
                    </label>

                    {{-- Is System --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_system" value="1" {{ old('is_system', $page->is_system) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               {{ $page->is_system ? 'disabled' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Strona systemowa</span>
                    </label>
                    @if($page->is_system)
                        <p class="text-xs text-gray-500">⚠️ Strony systemowe nie mogą być usunięte</p>
                    @else
                        <p class="text-xs text-gray-500">💡 Strony systemowe (np. polityka prywatności) nie mogą być usunięte</p>
                    @endif

                    {{-- Order --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kolejność wyświetlania
                        </label>
                        <input type="number" name="order" value="{{ old('order', $page->order) }}" min="0"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('order') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Powrót do listy
        </a>
        <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
            <i class="fa-solid fa-save"></i>
            Zapisz zmiany
        </button>
    </div>
</form>

<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// Initialize Quill Editor
var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['blockquote', 'code-block'],
            ['clean']
        ]
    },
    placeholder: 'Napisz treść strony...'
});

// Set initial content
var contentTextarea = document.getElementById('content');
if (contentTextarea.value) {
    quill.root.innerHTML = contentTextarea.value;
}

// Update hidden textarea on form submit
document.querySelector('form').addEventListener('submit', function() {
    contentTextarea.value = quill.root.innerHTML;
});

// Also update on any change (for better safety)
quill.on('text-change', function() {
    contentTextarea.value = quill.root.innerHTML;
});
</script>
@endsection

