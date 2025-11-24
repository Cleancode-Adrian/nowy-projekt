@extends('admin.layout')

@section('title', 'Biblioteka mediów')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
            <a href="{{ route('admin.media.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">storage/app/public</a>
            @if($currentPath)
                <span>/</span>
                <span class="text-gray-900">{{ $currentPath }}</span>
            @endif
            <span class="ml-auto text-gray-400">Łączny rozmiar: {{ $totalSize }}</span>
        </div>

        @if(!is_null($parentPath))
            <a href="{{ route('admin.media.index', ['dir' => $parentPath]) }}"
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <i class="fa-solid fa-arrow-left mr-2"></i> Powrót do katalogu nadrzędnego
            </a>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dodaj pliki</h3>
                <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Folder docelowy</label>
                        <input type="text"
                               name="folder"
                               value="{{ $currentPath }}"
                               placeholder="np. uploads/blog"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Pozostaw puste, aby wysłać do katalogu głównego.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pliki (max 5 MB każdy)</label>
                        <input type="file" name="files[]" multiple required class="w-full border border-gray-200 rounded-lg px-3 py-2">
                        @error('files.*')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
                        Prześlij
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Foldery</h3>
                    <span class="text-sm text-gray-500">{{ $directories->count() }} katalogów</span>
                </div>

                @if($directories->isEmpty())
                    <p class="text-sm text-gray-500">Brak podfolderów.</p>
                @else
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($directories as $directory)
                            <a href="{{ route('admin.media.index', ['dir' => $directory['path']]) }}"
                               class="border border-gray-200 rounded-xl p-4 hover:border-blue-400 transition-colors flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900 flex items-center gap-2">
                                        <i class="fa-solid fa-folder text-yellow-500"></i> {{ $directory['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $directory['total'] }} plików</p>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Pliki w tym katalogu</h3>
                <span class="text-sm text-gray-500">{{ $files->count() }} plików</span>
            </div>

            @if($files->isEmpty())
                <div class="text-center text-gray-500 py-12">
                    <i class="fa-regular fa-images text-4xl mb-4"></i>
                    <p>Brak plików do wyświetlenia.</p>
                    <p class="text-sm text-gray-400">Prześlij pliki, aby zacząć budować bibliotekę.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($files as $file)
                        @php $media = $file['media']; @endphp
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white flex flex-col">
                            <div class="relative bg-gray-50 h-44 flex items-center justify-center">
                                @if($file['is_image'])
                                    <img src="{{ $file['url'] }}" alt="{{ $media->alt_text ?? $file['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-file text-4xl text-gray-400"></i>
                                @endif
                                <span class="absolute top-3 right-3 bg-white/80 text-xs font-semibold px-2 py-1 rounded-full uppercase">
                                    {{ $file['extension'] ?: 'plik' }}
                                </span>
                                @if($media->width && $media->height)
                                    <span class="absolute bottom-3 right-3 bg-black/70 text-white text-[10px] px-2 py-1 rounded-full">
                                        {{ $media->width }}×{{ $media->height }}px
                                    </span>
                                @endif
                            </div>

                            <div class="p-4 flex-1 flex flex-col space-y-3">
                                <div>
                                    <p class="font-semibold text-gray-900 truncate" title="{{ $file['name'] }}">
                                        {{ $file['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $file['size'] }} • {{ $file['modified'] }}</p>
                                </div>

                                <div class="text-xs text-gray-500 space-y-1">
                                    <div class="flex items-start gap-2">
                                        <span class="font-semibold text-gray-700">URL:</span>
                                        <div class="flex-1 break-all">
                                            <a href="{{ $file['url'] }}" target="_blank" class="text-blue-600 break-words hover:underline">
                                                {{ $file['url'] }}
                                            </a>
                                            <button type="button"
                                                    class="ml-2 text-[11px] text-blue-600 hover:text-blue-800"
                                                    onclick="copyMediaUrl('{{ $file['url'] }}')">
                                                Kopiuj
                                            </button>
                                        </div>
                                    </div>
                                    @if($media->webp_path)
                                        <div class="flex items-start gap-2">
                                            <span class="font-semibold text-gray-700">WebP:</span>
                                            <div class="flex-1 break-all">
                                                <a href="{{ asset('storage/' . $media->webp_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                                    {{ asset('storage/' . $media->webp_path) }}
                                                </a>
                                                <button type="button"
                                                        class="ml-2 text-[11px] text-blue-600 hover:text-blue-800"
                                                        onclick="copyMediaUrl('{{ asset('storage/' . $media->webp_path) }}')">
                                                    Kopiuj
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-500 space-y-1">
                                    <div><span class="font-semibold text-gray-700">ALT:</span> {{ $media->alt_text ?? 'Brak' }}</div>
                                    <div>
                                        <span class="font-semibold text-gray-700">Tagi:</span>
                                        @if(!empty($media->tags))
                                            <span>
                                                @foreach($media->tags as $tag)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px] mr-1 mt-1">
                                                        #{{ $tag }}
                                                    </span>
                                                @endforeach
                                            </span>
                                        @else
                                            <span>Brak</span>
                                        @endif
                                    </div>
                                </div>

                                <form action="{{ route('admin.media.update', $media) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tekst alternatywny</label>
                                        <input type="text"
                                               name="alt_text"
                                               value="{{ old('alt_text', $media->alt_text) }}"
                                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tagi (oddziel przecinkami)</label>
                                        <input type="text"
                                               name="tags"
                                               value="{{ old('tags', $media->tags_list) }}"
                                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="np. blog, homepage, banner">
                                    </div>
                                    <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 rounded-lg">
                                        Zapisz metadane
                                    </button>
                                </form>

                                <div class="flex items-center gap-3">
                                    <a href="{{ $file['url'] }}" target="_blank"
                                       class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold py-2 rounded-lg">
                                        Podgląd
                                    </a>
                                    <form action="{{ route('admin.media.destroy') }}" method="POST"
                                          onsubmit="return confirm('Czy na pewno chcesz usunąć ten plik?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $file['path'] }}">
                                        <button type="submit"
                                                class="px-3 py-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 text-sm font-semibold">
                                            Usuń
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

<script>
    function copyMediaUrl(url) {
        if (!navigator.clipboard) {
            const tmp = document.createElement('input');
            tmp.style.position = 'absolute';
            tmp.style.left = '-9999px';
            tmp.value = url;
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
            alert('Skopiowano adres do schowka.');
            return;
        }

        navigator.clipboard.writeText(url)
            .then(() => alert('Skopiowano adres do schowka.'))
            .catch(() => alert('Nie udało się skopiować linku.'));
    }
</script>


