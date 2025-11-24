@extends('admin.layout')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            ✏️
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edytuj wpis</h1>
            <p class="text-gray-600">{{ $post->title }}</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.blog.update', $post) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Title --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-heading text-blue-600"></i>
                    Tytuł wpisu *
                </label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}"
                       class="w-full px-4 py-3 text-lg border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                @error('title') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-quote-left text-purple-600"></i>
                    Zajawka (opcjonalnie)
                </label>
                <textarea name="excerpt" rows="3"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-green-600"></i>
                    Treść wpisu *
                </label>
                <div id="editor-container" style="height: 400px;" class="bg-white border-2 border-gray-200 rounded-lg @error('content') border-red-500 @enderror"></div>
                <textarea id="content" name="content" class="hidden">{{ old('content', $post->content) }}</textarea>
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
                        <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="np. 10 Wskazówek dla Freelancerów - Zacznij Zarabiać Online"
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
                                  maxlength="160">{{ old('meta_description', $post->meta_description) }}</textarea>
                        @error('meta_description') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-2">💡 Wyświetla się w Google (maks. 160 znaków)</p>
                    </div>

                    {{-- Meta Keywords --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-key text-orange-600"></i>
                            Słowa Kluczowe (opcjonalnie)
                        </label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords ? implode(', ', $post->meta_keywords) : '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="freelancer, zlecenia, praca zdalna, freelancing">
                        @error('meta_keywords') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-2">💡 Oddziel przecinkami, np: "freelancer, programista, webdev"</p>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-900"><strong>Wskazówka:</strong> Jeśli pozostawisz puste, system użyje tytułu i zajawki artykułu.</p>
                </div>
            </div>
        </div>

        {{-- Sidebar Settings --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Publish Settings --}}
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl shadow-sm border-2 border-blue-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-blue-600"></i>
                    Publikacja
                </h3>
                <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>📝 Szkic</option>
                    <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>✅ Opublikowany</option>
                </select>
                @if($post->published_at)
                    <p class="text-xs text-gray-600 mt-3">
                        📅 Opublikowano: {{ $post->published_at->format('d.m.Y H:i') }}
                    </p>
                @endif
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-pink-600"></i>
                    Zdjęcie wyróżniające
                </h3>
                <input type="hidden" name="featured_image_existing" id="featured_image_existing_edit">

                @if($post->featured_image)
                    <div class="mb-4">
                        @if(str_starts_with($post->featured_image, 'http://') || str_starts_with($post->featured_image, 'https://'))
                            <img src="{{ $post->featured_image }}"
                                 alt="{{ $post->featured_image_alt ?? 'Obecne zdjęcie' }}"
                                 class="w-full h-40 object-cover rounded-lg border-2 border-gray-200"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'160\'%3E%3Crect fill=\'%23e5e7eb\' width=\'400\' height=\'160\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-size=\'48\'%3E📷%3C/text%3E%3C/svg%3E';">
                        @else
                            <img src="{{ asset('storage/' . $post->featured_image) }}"
                                 alt="{{ $post->featured_image_alt ?? 'Obecne zdjęcie' }}"
                                 class="w-full h-40 object-cover rounded-lg border-2 border-gray-200">
                        @endif
                        <p class="text-xs text-gray-500 mt-2 text-center">📸 Obecne zdjęcie wyróżniające</p>
                    </div>
                @else
                    <div class="mb-4 p-8 bg-gray-50 rounded-lg text-center">
                        <i class="fa-solid fa-image text-6xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Brak zdjęcia wyróżniającego</p>
                    </div>
                @endif

                <label for="featured_image_input"
                       class="block border-2 border-dashed border-blue-300 rounded-lg p-6 text-center hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <i class="fa-solid fa-cloud-arrow-up text-5xl text-blue-400 mb-3 block"></i>
                    <div class="text-sm font-semibold text-blue-600 mb-2">Kliknij, aby wybrać plik</div>
                    <p class="text-xs text-gray-500">
                        lub przeciągnij i upuść tutaj
                    </p>
                    <p class="text-xs text-gray-500 mt-3">
                        Maks 2MB • JPG, PNG, WebP • Zalecany: 1200x630px
                    </p>
                    <span id="selected-file-name-edit" class="mt-3 text-xs text-blue-600 font-semibold hidden"></span>
                </label>
                <input type="file"
                       name="featured_image"
                       id="featured_image_input"
                       accept="image/*"
                       class="hidden"
                       onchange="previewImage(this, 'preview-img-edit', 'image-preview-edit', 'selected-file-name-edit')">
                @error('featured_image')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror

                <div id="image-preview-edit" class="mt-4 hidden">
                    <div class="bg-green-50 border-2 border-green-500 rounded-lg p-2">
                        <p class="text-xs text-green-700 mb-2 text-center font-semibold">✅ Nowy obrazek wybrany (zapisz formularz aby zastosować)</p>
                        <img id="preview-img-edit" class="w-full h-48 object-cover rounded-lg">
                    </div>
                </div>
                @include('admin.media.picker-modal', [
                    'fieldId' => 'featured_image_existing_edit',
                    'buttonText' => 'Wybierz z biblioteki mediów',
                    'buttonClass' => 'w-full mt-4 border border-blue-200 text-blue-700 font-semibold py-2 rounded-lg hover:bg-blue-50 transition-colors',
                    'previewImageId' => 'preview-img-edit',
                    'previewWrapperId' => 'image-preview-edit',
                    'fileNameId' => 'selected-file-name-edit'
                ])
                <button type="button"
                        class="mt-3 text-sm text-gray-600 hover:text-gray-900 underline"
                        onclick="clearSelectedImage('featured_image_input','featured_image_existing_edit','selected-file-name-edit','image-preview-edit')">
                    Wyczyść wybrane zdjęcie
                </button>

                {{-- Alt Text for Image --}}
                <div class="mt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-text-width text-green-600"></i>
                        Tekst alternatywny (Alt Text)
                    </label>
                    <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Opisz obrazek dla SEO i dostępności (np. 'Grafika przedstawiająca...')"
                           maxlength="255">
                    <p class="text-xs text-gray-500 mt-2">💡 Ważne dla SEO i dostępności - opisz co pokazuje obrazek</p>
                    @error('featured_image_alt') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-folder text-blue-600"></i>
                    Kategoria
                </h3>
                <select name="category_id" id="category_select" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Wybierz kategorię --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-2 mb-3">💡 Wybierz główną kategorię artykułu</p>

                <div class="mt-3 pt-3 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-plus text-green-600"></i> Lub dodaj nową kategorię:
                    </label>
                    <input type="text" name="new_category" value="{{ old('new_category') }}"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Nazwa nowej kategorii">
                    <p class="text-xs text-gray-500 mt-1">💡 Jeśli wpiszesz nazwę, nowa kategoria zostanie utworzona</p>
                </div>
                @error('category_id') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                @error('new_category') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-orange-600"></i>
                    Tagi
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto mb-4">
                    @foreach($tags as $tag)
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   {{ $post->tags->contains($tag->id) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                            <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mb-3">💡 Wybierz tagi opisujące artykuł (można wybrać wiele)</p>

                <div class="pt-3 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-plus text-green-600"></i> Lub dodaj nowe tagi:
                    </label>
                    <input type="text" name="new_tags" value="{{ old('new_tags') }}"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Tagi oddzielone przecinkami (np. AI, SEO, Marketing)">
                    <p class="text-xs text-gray-500 mt-1">💡 Wpisz tagi oddzielone przecinkami - nowe tagi zostaną utworzone</p>
                </div>
                @error('new_tags') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Stats --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple"></i>
                    Statystyki
                </h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span>Wyświetlenia:</span>
                        <strong>{{ $post->views_count }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Utworzono:</span>
                        <strong>{{ $post->created_at->format('d.m.Y') }}</strong>
                    </div>
                    @if($post->updated_at != $post->created_at)
                        <div class="flex justify-between">
                            <span>Edytowano:</span>
                            <strong>{{ $post->updated_at->format('d.m.Y H:i') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-8 flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <a href="{{ route('admin.blog.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Powrót do listy
        </a>
        <div class="flex items-center gap-4">
            @if($post->status === 'published')
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                   class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i>
                    Zobacz wpis
                </a>
            @endif
            <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                Zapisz zmiany
            </button>
        </div>
    </div>
</form>

<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
function previewImage(input, previewImgId = 'preview-img-edit', previewWrapperId = 'image-preview-edit', fileNameId = 'selected-file-name-edit') {
    const preview = document.getElementById(previewWrapperId);
    const img = document.getElementById(previewImgId);
    const fileName = document.getElementById(fileNameId);

    if (!img || !preview) {
        return;
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);

        if (fileName) {
            fileName.textContent = input.files[0].name;
            fileName.classList.remove('hidden');
        }
    } else {
        preview.classList.add('hidden');
        if (fileName) {
            fileName.textContent = '';
            fileName.classList.add('hidden');
        }
    }
}

function clearSelectedImage(fileInputId, existingFieldId, fileNameId, previewWrapperId) {
    const fileInput = document.getElementById(fileInputId);
    const existingField = document.getElementById(existingFieldId);
    const fileName = document.getElementById(fileNameId);
    const preview = document.getElementById(previewWrapperId);

    if (fileInput) {
        fileInput.value = '';
    }
    if (existingField) {
        existingField.value = '';
    }
    if (fileName) {
        fileName.textContent = '';
        fileName.classList.add('hidden');
    }
    if (preview) {
        preview.classList.add('hidden');
    }
}

// Initialize Quill Editor
var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['clean']
        ]
    },
    placeholder: 'Napisz treść artykułu...'
});

// Set initial content if exists
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

