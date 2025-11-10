<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4">
                ← Powrót do panelu
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">✏️ Edytuj ogłoszenie</h1>
            <p class="text-gray-600">{{ $announcement->title }}</p>
        </div>

        {{-- Form --}}
        <form wire:submit.prevent="update">
            <div class="card space-y-6">

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tytuł ogłoszenia *
                    </label>
                    <input type="text" id="title" wire:model="title"
                           class="input @error('title') border-red-500 @enderror">
                    @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Opis projektu *
                    </label>
                    <textarea id="description" wire:model="description" rows="10"
                              class="input @error('description') border-red-500 @enderror"></textarea>
                    @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
                    @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Budget --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="budget_min" class="block text-sm font-semibold text-gray-700 mb-2">Budżet od (PLN)</label>
                        <input type="number" id="budget_min" wire:model="budget_min" class="input" min="0" step="100">
                    </div>
                    <div>
                        <label for="budget_max" class="block text-sm font-semibold text-gray-700 mb-2">Budżet do (PLN)</label>
                        <input type="number" id="budget_max" wire:model="budget_max" class="input" min="0" step="100">
                    </div>
                </div>

                {{-- Deadline & Location --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">Termin realizacji</label>
                        <input type="date" id="deadline" wire:model="deadline" class="input">
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Lokalizacja</label>
                        <input type="text" id="location" wire:model="location" class="input">
                    </div>
                </div>

                {{-- Tags --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Technologie</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($tags as $tag)
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="selectedTags" value="{{ $tag->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Urgent --}}
                <div class="flex items-center">
                    <input type="checkbox" id="is_urgent" wire:model="is_urgent"
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-5 h-5">
                    <label for="is_urgent" class="ml-3 flex items-center cursor-pointer">
                        <span class="text-2xl mr-2">🔥</span>
                        <span class="text-sm font-semibold text-gray-900">Projekt pilny</span>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="pt-6 border-t border-gray-200 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                        Anuluj
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                        💾 Zapisz zmiany
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

