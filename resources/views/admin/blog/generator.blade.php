@extends('admin.layout')

@section('title', 'Generator Wpisów Blogowych')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">🤖 Generator Wpisów Blogowych</h1>
    <p class="text-gray-600">Automatyczne generowanie wpisów z OpenAI</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            </div>
        </div>
    </div>
@endif

@if(session('errors'))
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    @foreach(session('errors') as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            </div>
        </div>
    </div>
@endif

@if(session('generated'))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
        <h3 class="font-semibold text-blue-900 mb-2">✅ Wygenerowane wpisy:</h3>
        <ul class="list-disc list-inside text-sm text-blue-700">
            @foreach(session('generated') as $post)
                <li><a href="{{ route('admin.blog.edit', $post->id) }}" class="underline">{{ $post->title }}</a></li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Ustawienia API Keys --}}
    <div class="card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">🔑 Klucze API</h2>

        <form method="POST" action="{{ route('admin.blog.generator.api-keys') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    OpenAI API Key
                </label>
                <input type="password"
                       name="openai_api_key"
                       value="{{ $openaiKey }}"
                       placeholder="sk-..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">
                    <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">
                        Pobierz klucz z OpenAI →
                    </a>
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Unsplash Access Key (opcjonalne)
                </label>
                <input type="password"
                       name="unsplash_access_key"
                       value="{{ $unsplashKey }}"
                       placeholder="..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">
                    <a href="https://unsplash.com/developers" target="_blank" class="text-blue-600 hover:underline">
                        Pobierz klucz z Unsplash →
                    </a>
                </p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                💾 Zapisz klucze
            </button>
        </form>
    </div>

    {{-- Generator wpisów --}}
    <div class="card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📝 Generuj wpisy</h2>

        <form method="POST" action="{{ route('admin.blog.generator.generate') }}" id="generateForm">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tematy wpisów (jeden na linię)
                </label>
                <textarea
                    name="topics"
                    rows="6"
                    placeholder="Jak znaleźć pierwszych klientów jako freelancer&#10;Najlepsze narzędzia automatyzacji dla freelancerów&#10;Jak ustalać stawki jako freelancer"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required></textarea>
                <p class="text-xs text-gray-500 mt-1">Każdy temat w osobnej linii. Możesz podać wiele tematów.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Liczba wpisów
                    </label>
                    <input type="number"
                           name="count"
                           value="1"
                           min="1"
                           max="10"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kategoria
                    </label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Auto (domyślna)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tagi (oddzielone przecinkami, opcjonalne)
                </label>
                <input type="text"
                       name="tags"
                       placeholder="Freelancing, Marketing, AI"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-4 space-y-2">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="download_image"
                           value="1"
                           checked
                           class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">Pobierz obrazek z Unsplash</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox"
                           name="test_mode"
                           value="1"
                           class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">Tryb testowy (szkic, nie publikuj)</span>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button type="submit" 
                        id="generateBtn"
                        class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-3 px-4 rounded-lg transition-all">
                    <span id="btnText">🚀 Generuj</span>
                    <span id="btnLoading" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> ...
                    </span>
                </button>
                
                <button type="button"
                        onclick="document.getElementById('runNowForm').submit()"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-4 rounded-lg transition-all">
                    ⚡ Teraz
                </button>
            </div>
        </form>
        
        {{-- Formularz do natychmiastowego uruchomienia --}}
        <form method="POST" action="{{ route('admin.blog.generator.run-now') }}" id="runNowForm" class="hidden">
            @csrf
            <input type="hidden" name="topics" id="runNowTopics">
            <input type="hidden" name="count" id="runNowCount" value="1">
            <input type="hidden" name="category_id" id="runNowCategory">
            <input type="hidden" name="tags" id="runNowTags">
            <input type="hidden" name="download_image" value="1" checked>
            <input type="hidden" name="test_mode" value="0">
        </form>
    </div>
    
    {{-- Harmonogram --}}
    <div class="card">
        <h2 class="text-xl font-bold text-gray-900 mb-4">⏰ Harmonogram</h2>
        
        <form method="POST" action="{{ route('admin.blog.generator.schedule') }}">
            @csrf
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_enabled" 
                           value="1"
                           {{ $schedule->is_enabled ? 'checked' : '' }}
                           class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm font-medium text-gray-700">Włącz automatyczne generowanie</span>
                </label>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Godzina
                    </label>
                    <input type="time" 
                           name="time" 
                           value="{{ $schedule->time ?? '09:00' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Częstotliwość
                    </label>
                    <select name="frequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="daily" {{ ($schedule->frequency ?? 'daily') === 'daily' ? 'selected' : '' }}>Codziennie</option>
                        <option value="twice_daily" {{ ($schedule->frequency ?? '') === 'twice_daily' ? 'selected' : '' }}>2x dziennie</option>
                        <option value="weekdays" {{ ($schedule->frequency ?? '') === 'weekdays' ? 'selected' : '' }}>Dni robocze</option>
                        <option value="weekly" {{ ($schedule->frequency ?? '') === 'weekly' ? 'selected' : '' }}>Raz w tygodniu</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Liczba wpisów na raz
                </label>
                <input type="number" 
                       name="count" 
                       value="{{ $schedule->count ?? 1 }}"
                       min="1"
                       max="10"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tematy (jeden na linię)
                </label>
                <textarea 
                    name="topics" 
                    rows="4"
                    placeholder="Jak znaleźć pierwszych klientów&#10;Najlepsze narzędzia automatyzacji&#10;Jak ustalać stawki"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $schedule->topics ?? '' }}</textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kategoria
                </label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Auto (domyślna)</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ ($schedule->category_id ?? null) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tagi (oddzielone przecinkami)
                </label>
                <input type="text" 
                       name="tags" 
                       value="{{ $schedule->tags ?? '' }}"
                       placeholder="Freelancing, Marketing"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="mb-4 space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="download_image" 
                           value="1"
                           {{ ($schedule->download_image ?? true) ? 'checked' : '' }}
                           class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">Pobierz obrazek</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="auto_publish" 
                           value="1"
                           {{ ($schedule->auto_publish ?? true) ? 'checked' : '' }}
                           class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">Auto-publikuj (bez zaznaczenia = szkic)</span>
                </label>
            </div>
            
            @if($schedule->last_run_at)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600">
                        <strong>Ostatnie uruchomienie:</strong><br>
                        {{ $schedule->last_run_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            @endif
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                💾 Zapisz harmonogram
            </button>
        </form>
    </div>
</div>

{{-- Przykładowe tematy --}}
<div class="card mt-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-3">💡 Przykładowe tematy</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @php
            $exampleTopics = [
                'Jak znaleźć pierwszych klientów jako freelancer w 2025',
                'Najlepsze narzędzia automatyzacji dla freelancerów',
                'Jak ustalać stawki jako freelancer - kompletny przewodnik',
                'Time management dla freelancerów - 10 sprawdzonych metod',
                'Jak budować portfolio freelancera, które przyciąga klientów',
                'Fakturowanie i podatki dla freelancerów w Polsce',
                'Work-life balance w freelancingu - jak nie wypalić się',
                'Jak negocjować z klientami - praktyczne wskazówki',
                'Najlepsze platformy freelancerskie w 2025',
                'Marketing dla freelancerów - jak zdobywać klientów',
            ];
        @endphp
        @foreach($exampleTopics as $topic)
            <button type="button"
                    onclick="addTopic('{{ $topic }}')"
                    class="text-left px-3 py-2 text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 rounded border border-gray-200 transition-colors">
                {{ $topic }}
            </button>
        @endforeach
    </div>
</div>

<script>
function addTopic(topic) {
    const textarea = document.querySelector('textarea[name="topics"]');
    const currentValue = textarea.value.trim();
    if (currentValue && !currentValue.endsWith('\n')) {
        textarea.value += '\n';
    }
    textarea.value += topic + '\n';
    textarea.focus();
}

// Przygotuj dane do natychmiastowego uruchomienia
document.getElementById('generateForm').addEventListener('submit', function(e) {
    const form = e.target;
    const runNowForm = document.getElementById('runNowForm');
    
    runNowForm.querySelector('#runNowTopics').value = form.querySelector('[name="topics"]').value;
    runNowForm.querySelector('#runNowCount').value = form.querySelector('[name="count"]').value || '1';
    runNowForm.querySelector('#runNowCategory').value = form.querySelector('[name="category_id"]').value || '';
    runNowForm.querySelector('#runNowTags').value = form.querySelector('[name="tags"]').value || '';
});

document.getElementById('generateForm').addEventListener('submit', function() {
    const btn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    btn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
});
</script>
@endsection

