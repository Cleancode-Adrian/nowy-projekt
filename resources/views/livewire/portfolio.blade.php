<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">💼 Moje Portfolio</h1>
                <p class="text-gray-600">Pokaż swoje najlepsze realizacje</p>
            </div>
            <button wire:click="toggleForm" class="btn btn-primary">
                @if($showForm)
                    ❌ Anuluj
                @else
                    ➕ Dodaj projekt
                @endif
            </button>
        </div>

        {{-- Form --}}
        @if($showForm)
            <div class="card mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">
                    {{ $editingId ? '✏️ Edytuj projekt' : '➕ Nowy projekt' }}
                </h3>

                <form wire:submit="save" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tytuł projektu *</label>
                        <input type="text" wire:model="title" class="input @error('title') border-red-500 @enderror"
                               placeholder="np. Strona dla firmy XYZ">
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Opis *</label>
                        <textarea wire:model="description" rows="5" class="input @error('description') border-red-500 @enderror"
                                  placeholder="Opisz zakres projektu, Twoją rolę, użyte technologie..."></textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Zdjęcie projektu</label>
                        <input type="file" wire:model="image" accept="image/*" class="input">
                        @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        @if($image)
                            <p class="text-sm text-gray-600 mt-1">✅ Wybrano: {{ $image->getClientOriginalName() }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Link do projektu</label>
                            <input type="url" wire:model="url" class="input" placeholder="https://example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data zakończenia</label>
                            <input type="date" wire:model="completed_at" class="input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Technologie (oddziel przecinkami)</label>
                        <input type="text" wire:model="technologies" class="input"
                               placeholder="React, Laravel, Tailwind CSS">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_featured" wire:model="is_featured"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                        <label for="is_featured" class="ml-3 text-sm font-semibold text-gray-900 cursor-pointer">
                            ⭐ Projekt wyróżniony (priorytetowe wyświetlanie)
                        </label>
                    </div>

                    <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>💾 Zapisz projekt</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Zapisywanie...</span>
                        </button>
                        <button type="button" wire:click="toggleForm" class="text-gray-600 hover:text-gray-900 font-medium">
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Portfolio Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($portfolioItems as $item)
                <div class="card relative {{ $item->is_featured ? 'ring-2 ring-yellow-400' : '' }}">
                    @if($item->is_featured)
                        <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">
                            ⭐ Wyróżniony
                        </div>
                    @endif

                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}"
                             class="w-full h-48 object-cover rounded-lg mb-4">
                    @else
                        <div class="w-full h-48 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg mb-4 flex items-center justify-center text-white text-6xl">
                            💼
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                    <p class="text-gray-700 text-sm mb-4 line-clamp-3">{{ $item->description }}</p>

                    @if($item->technologies)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($item->technologies as $tech)
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            @if($item->completed_at)
                                {{ $item->completed_at->format('m/Y') }}
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($item->url)
                                <a href="{{ $item->url }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    🔗 Zobacz
                                </a>
                            @endif
                            <button wire:click="edit({{ $item->id }})"
                                    class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                ✏️ Edytuj
                            </button>
                            <button wire:click="delete({{ $item->id }})"
                                    wire:confirm="Czy na pewno chcesz usunąć ten projekt?"
                                    class="text-red-600 hover:text-red-700 text-sm font-medium">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 bg-white rounded-xl">
                    <div class="text-6xl mb-4">💼</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak projektów w portfolio</h3>
                    <p class="text-gray-600 mb-6">Dodaj swoje realizacje aby zwiększyć szanse na zlecenia</p>
                    <button wire:click="toggleForm" class="btn btn-primary">
                        ➕ Dodaj pierwszy projekt
                    </button>
                </div>
            @endforelse
        </div>

    </div>
</div>

