<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Filters --}}
        <div class="card mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">🔍 Wyszukiwanie zaawansowane</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-3">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="input" placeholder="Szukaj po tytule lub opisie...">
                </div>

                <div>
                    <select wire:model.live="category_id" class="input">
                        <option value="">Wszystkie kategorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <input type="number" wire:model.live="budget_min" class="input" placeholder="Budżet od (PLN)">
                </div>

                <div>
                    <input type="number" wire:model.live="budget_max" class="input" placeholder="Budżet do (PLN)">
                </div>

                <div>
                    <input type="number" wire:model.live="hourly_rate_min" class="input" placeholder="Stawka godzinowa od (PLN/h)">
                </div>

                <div>
                    <input type="number" wire:model.live="hourly_rate_max" class="input" placeholder="Stawka godzinowa do (PLN/h)">
                </div>

                <div>
                    <select wire:model.live="days_ago" class="input">
                        <option value="">Wszystkie daty</option>
                        <option value="1">Ostatnie 24h</option>
                        <option value="7">Ostatni tydzień</option>
                        <option value="30">Ostatni miesiąc</option>
                    </select>
                </div>

                <div>
                    <select wire:model.live="sort" class="input">
                        <option value="newest">Najnowsze</option>
                        <option value="oldest">Najstarsze</option>
                        <option value="budget_high">Budżet: malejąco</option>
                        <option value="budget_low">Budżet: rosnąco</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model.live="is_urgent" id="is_urgent" class="rounded mr-2">
                    <label for="is_urgent" class="text-sm font-medium text-gray-700">🔥 Tylko pilne</label>
                </div>

                @auth
                    <div class="lg:col-span-3 flex items-center justify-between pt-4 border-t border-gray-200">
                        <div>
                            @if($showSaveForm)
                                <div class="flex items-center gap-2">
                                    <input type="text" wire:model="saveSearchName"
                                           class="input w-64"
                                           placeholder="Nazwa zapisanego wyszukiwania"
                                           wire:keydown.enter="saveSearch">
                                    <button wire:click="saveSearch" class="btn btn-primary">
                                        💾 Zapisz
                                    </button>
                                    <button wire:click="$set('showSaveForm', false)" class="btn btn-secondary">
                                        Anuluj
                                    </button>
                                </div>
                            @else
                                <button wire:click="$set('showSaveForm', true)" class="btn btn-secondary">
                                    💾 Zapisz to wyszukiwanie
                                </button>
                            @endif
                        </div>
                    </div>
                @endauth
            </div>

            {{-- Saved Searches --}}
            @auth
                @if($savedSearches->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📌 Zapisane wyszukiwania</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($savedSearches as $saved)
                                <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                                    <button wire:click="loadSavedSearch({{ $saved->id }})"
                                            class="text-sm font-medium text-blue-700 hover:text-blue-900">
                                        {{ $saved->name }}
                                    </button>
                                    <button wire:click="deleteSavedSearch({{ $saved->id }})"
                                            wire:confirm="Usunąć to zapisane wyszukiwanie?"
                                            class="text-red-500 hover:text-red-700 text-xs">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        {{-- Recommendations --}}
        @if(isset($recommendations) && $recommendations->count() > 0)
            <div class="card mb-8 bg-gradient-to-r from-purple-50 to-blue-50 border-purple-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4">⭐ Rekomendowane dla Ciebie</h3>
                <p class="text-sm text-gray-600 mb-4">Ogłoszenia dopasowane do Twojej historii</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recommendations as $announcement)
                        <x-announcement-card :announcement="$announcement" />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Results --}}
        <div class="mb-4">
            <p class="text-gray-600">Znaleziono: <strong>{{ $announcements->total() }}</strong> ogłoszeń</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($announcements as $announcement)
                <x-announcement-card :announcement="$announcement" />
            @empty
                <div class="col-span-3 text-center py-16 bg-white rounded-xl">
                    <p class="text-gray-500">Brak wyników</p>
                </div>
            @endforelse
        </div>

        {{ $announcements->links() }}
    </div>
</div>

