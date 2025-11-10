<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4">
                ← Powrót do panelu
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📝 Dodaj nowe ogłoszenie</h1>
            <p class="text-gray-600">Wypełnij formularz, a Twoje ogłoszenie zostanie opublikowane po zatwierdzeniu przez administratora.</p>
        </div>

        {{-- Success Message --}}
        @if(session()->has('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
                <p class="text-green-900">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit.prevent="submit">
            <div class="card space-y-6">

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tytuł ogłoszenia *
                    </label>
                    <input type="text" id="title" wire:model="title"
                           class="input @error('title') border-red-500 @enderror"
                           placeholder="np. Stworzenie strony internetowej dla firmy budowlanej">
                    @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Minimum 10 znaków</p>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Opis projektu *
                    </label>
                    <textarea id="description" wire:model="description" rows="10"
                              class="input @error('description') border-red-500 @enderror"
                              placeholder="Szczegółowo opisz projekt: zakres prac, wymagania, oczekiwania..."></textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Minimum 50 znaków. Im więcej szczegółów, tym lepsze oferty otrzymasz.</p>
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategoria *
                    </label>
                    <select id="category_id" wire:model="category_id"
                            class="input @error('category_id') border-red-500 @enderror">
                        <option value="">-- Wybierz kategorię --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Budget --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Budżet (opcjonalnie)
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="budget_min" class="block text-xs text-gray-600 mb-1">Od (PLN)</label>
                            <input type="number" id="budget_min" wire:model="budget_min"
                                   class="input @error('budget_min') border-red-500 @enderror"
                                   placeholder="np. 1000" min="0" step="100">
                            @error('budget_min')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="budget_max" class="block text-xs text-gray-600 mb-1">Do (PLN)</label>
                            <input type="number" id="budget_max" wire:model="budget_max"
                                   class="input @error('budget_max') border-red-500 @enderror"
                                   placeholder="np. 5000" min="0" step="100">
                            @error('budget_max')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">Pozostaw puste jeśli chcesz "Do uzgodnienia"</p>
                </div>

                {{-- Deadline & Location --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">
                            Termin realizacji
                        </label>
                        <input type="date" id="deadline" wire:model="deadline"
                               class="input @error('deadline') border-red-500 @enderror">
                        @error('deadline')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                            Lokalizacja
                        </label>
                        <input type="text" id="location" wire:model="location"
                               class="input @error('location') border-red-500 @enderror"
                               placeholder="np. Warszawa, Zdalnie">
                    </div>
                </div>

                {{-- Tags --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Technologie / Umiejętności (opcjonalnie)
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($tags as $tag)
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="selectedTags" value="{{ $tag->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedTags')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-2">Wybierz maksymalnie 10 tagów</p>
                </div>

                {{-- Attachments --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📎 Załączniki (opcjonalnie)
                    </label>
                    <input type="file" wire:model="attachments" multiple
                           accept=".pdf,.jpg,.jpeg,.png,.zip,.doc,.docx"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('attachments.*')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">
                        Dozwolone: PDF, JPG, PNG, ZIP, DOC, DOCX (max 10MB/plik)
                    </p>

                    @if(!empty($attachments))
                        <div class="mt-3 space-y-2">
                            @foreach($attachments as $index => $file)
                                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                    <i class="fa-solid fa-file"></i>
                                    <span class="flex-1">{{ $file->getClientOriginalName() }}</span>
                                    <span class="text-xs text-gray-500">{{ round($file->getSize() / 1024, 1) }} KB</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Urgent --}}
                <div class="flex items-center">
                    <input type="checkbox" id="is_urgent" wire:model="is_urgent"
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-5 h-5">
                    <label for="is_urgent" class="ml-3 flex items-center cursor-pointer">
                        <span class="text-2xl mr-2">🔥</span>
                        <span class="text-sm font-semibold text-gray-900">Projekt pilny</span>
                        <span class="text-xs text-gray-500 ml-2">(będzie wyróżniony)</span>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="pt-6 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        * - pola wymagane
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                            Anuluj
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors shadow-md hover:shadow-lg">
                            📤 Wyślij ogłoszenie
                        </button>
                    </div>
                </div>

            </div>
        </form>

        {{-- Info Box --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-bold text-blue-900 mb-3">ℹ️ Informacje</h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li>• Twoje ogłoszenie zostanie sprawdzone przez administratora (zwykle do 24h)</li>
                <li>• Po zatwierdzeniu pojawi się publicznie i freelancerzy będą mogli składać oferty</li>
                <li>• Otrzymasz powiadomienie email gdy ogłoszenie zostanie opublikowane</li>
                <li>• Możesz usunąć ogłoszenie w każdej chwili w swoim panelu</li>
            </ul>
        </div>

    </div>
</div>

