@extends('admin.layout')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            📝
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dodaj wpis na bloga</h1>
            <p class="text-gray-600">Stwórz nowy artykuł dla użytkowników</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Title --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-heading text-blue-600"></i>
                    Tytuł wpisu *
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full px-4 py-3 text-lg border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                       placeholder="np. 10 wskazówek dla początkujących freelancerów">
                @error('title') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-quote-left text-purple-600"></i>
                    Zajawka (opcjonalnie)
                </label>
                <textarea name="excerpt" rows="3"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('excerpt') border-red-500 @enderror"
                          placeholder="Krótki opis artykułu wyświetlany w liście i dla SEO (max 500 znaków)">{{ old('excerpt') }}</textarea>
                @error('excerpt') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-2">💡 Użyj 1-2 zdań aby zachęcić do czytania</p>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-green-600"></i>
                    Treść wpisu *
                </label>
                <div id="editor-container" style="height: 400px;" class="bg-white border-2 border-gray-200 rounded-lg @error('content') border-red-500 @enderror"></div>
                <textarea id="content" name="content" class="hidden">{{ old('content') }}</textarea>
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
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}"
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
                                  maxlength="160">{{ old('meta_description') }}</textarea>
                        @error('meta_description') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-2">💡 Wyświetla się w Google (maks. 160 znaków)</p>
                    </div>

                    {{-- Meta Keywords --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-key text-orange-600"></i>
                            Słowa Kluczowe (opcjonalnie)
                        </label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
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
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="draft">📝 Szkic (nie opublikowany)</option>
                        <option value="published">✅ Opublikowany (widoczny publicznie)</option>
                    </select>
                    <p class="text-xs text-gray-600 mt-2">
                        💡 Szkic: tylko Ty widzisz<br>
                        ✅ Opublikowany: wszyscy widzą
                    </p>
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-pink-600"></i>
                    Zdjęcie wyróżniające
                </h3>
                <input type="hidden" name="featured_image_existing" id="featured_image_existing_create">
                <label for="featured_image_input_create"
                       class="block border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                    <div class="text-sm font-semibold text-blue-600 mb-2">Kliknij, aby wybrać plik</div>
                    <p class="text-xs text-gray-500">lub przeciągnij i upuść tutaj</p>
                    <p class="text-xs text-gray-500 mt-3">
                        📐 Zalecany rozmiar: <strong>1200x630px</strong><br>
                        📦 Maks: <strong>2MB</strong> (JPG, PNG, WebP)
                    </p>
                    <span id="selected-file-name-create" class="mt-3 text-xs text-blue-600 font-semibold hidden"></span>
                </label>
                <input type="file" name="featured_image" accept="image/*"
                       id="featured_image_input_create"
                       class="hidden"
                       onchange="previewImage(this, 'preview-img-create', 'image-preview-create', 'selected-file-name-create')">
                @error('featured_image') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                <div id="image-preview-create" class="mt-4 hidden">
                    <img id="preview-img-create" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">
                </div>
                @include('admin.media.picker-modal', [
                    'fieldId' => 'featured_image_existing_create',
                    'buttonText' => 'Wybierz z biblioteki mediów',
                    'buttonClass' => 'w-full mt-4 border border-blue-200 text-blue-700 font-semibold py-2 rounded-lg hover:bg-blue-50 transition-colors',
                    'previewImageId' => 'preview-img-create',
                    'previewWrapperId' => 'image-preview-create',
                    'fileNameId' => 'selected-file-name-create'
                ])
                <button type="button"
                        class="mt-3 text-sm text-gray-600 hover:text-gray-900 underline"
                        onclick="clearSelectedImage('featured_image_input_create','featured_image_existing_create','selected-file-name-create','image-preview-create')">
                    Wyczyść wybrane zdjęcie
                </button>
            </div>

            {{-- Tags --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-orange-600"></i>
                    Tagi/Kategorie
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($tags as $tag)
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                            <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-3">💡 Wybierz tematykę artykułu</p>
            </div>

            {{-- Tips --}}
            <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-6">
                <h4 class="font-bold text-yellow-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb"></i>
                    Wskazówki SEO
                </h4>
                <ul class="space-y-2 text-sm text-yellow-800">
                    <li>✅ Tytuł: 50-60 znaków</li>
                    <li>✅ Excerpt: 150-160 znaków</li>
                    <li>✅ Użyj słów kluczowych</li>
                    <li>✅ Dodaj zdjęcie (1200x630px)</li>
                    <li>✅ Wybierz trafne tagi</li>
                </ul>
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
            <button type="submit" name="status" value="draft"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                Zapisz jako szkic
            </button>
            <button type="submit" name="status" value="published"
                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fa-solid fa-rocket"></i>
                Opublikuj teraz
            </button>
        </div>
    </div>
</form>

<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>
function previewImage(input, previewImgId = 'preview-img-create', previewWrapperId = 'image-preview-create', fileNameId = 'selected-file-name-create') {
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
</script>

<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
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

