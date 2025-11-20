<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Przeglądaj ogłoszenia</h1>
            <p class="text-gray-600">{{ $announcements->total() }} aktywnych projektów</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Search --}}
                <div class="md:col-span-2">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="🔍 Szukaj ogłoszeń..."
                        class="input">
                </div>

                {{-- Category --}}
                <div>
                    <select wire:model.live="category" class="input">
                        <option value="">Wszystkie kategorie</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Clear Filters --}}
                <div>
                    <button wire:click="clearFilters" type="button" class="w-full input text-center cursor-pointer hover:bg-gray-50">
                        ✖ Wyczyść filtry
                    </button>
                </div>
            </div>

            {{-- Advanced Filters (collapsible) --}}
            <div x-data="{ showAdvanced: false }" class="mt-4">
                <button @click="showAdvanced = !showAdvanced" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <span x-show="!showAdvanced">+ Filtry zaawansowane</span>
                    <span x-show="showAdvanced">- Ukryj filtry</span>
                </button>

                <div x-show="showAdvanced" x-transition class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Budget Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budżet minimalny</label>
                        <input type="number" wire:model.live.debounce.500ms="minBudget" placeholder="np. 1000" class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budżet maksymalny</label>
                        <input type="number" wire:model.live.debounce.500ms="maxBudget" placeholder="np. 10000" class="input">
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Indicator --}}
        <div wire:loading class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="text-gray-600 mt-2">Ładowanie...</p>
        </div>

        {{-- Announcements Grid --}}
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($announcements as $announcement)
                <x-announcement-card :announcement="$announcement" />
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak ogłoszeń</h3>
                    <p class="text-gray-600 mb-6">Nie znaleźliśmy ogłoszeń spełniających Twoje kryteria</p>
                    <button wire:click="clearFilters" class="btn btn-primary">
                        Wyczyść filtry
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $announcements->links() }}
        </div>
    </div>
</div>

{{-- Schema.org for Job Listings --}}
@if($announcements->count() > 0)
@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "itemListElement": [
        @foreach($announcements as $index => $announcement)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "JobPosting",
                "title": "{{ $announcement->title }}",
                "description": "{{ Str::limit($announcement->description, 200) }}",
                "datePosted": "{{ $announcement->created_at->toIso8601String() }}",
                "hiringOrganization": {
                    "@@type": "Organization",
                    "name": "{{ $announcement->user->company ?? $announcement->user->name }}"
                },
                "jobLocation": {
                    "@@type": "Place",
                    "address": "{{ $announcement->location ?? 'Zdalna' }}"
                }
            }
        }{{ $loop->last ? '' : ',' }}
        @endforeach
    ]
}
</script>
@endpush
@endif

