<div>
    {{-- Nagłówek strony --}}
    <div class="border-b border-ink-100 bg-white">
        <div class="container-page py-10">
            <nav class="text-sm text-ink-400" aria-label="Okruszki">
                <a href="{{ route('home') }}" class="hover:text-ink-700">Strona główna</a>
                <span class="mx-1.5">/</span>
                <span class="text-ink-600">Zlecenia</span>
            </nav>
            <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Zlecenia</h1>
                    <p class="mt-1 text-ink-500">
                        <span class="font-semibold text-ink-800">{{ number_format($announcements->total(), 0, ',', ' ') }}</span>
                        {{ $announcements->total() === 1 ? 'aktywny projekt' : 'aktywnych projektów' }} czeka na oferty
                    </p>
                </div>
                @auth
                    @if(auth()->user()->isClient())
                        <a href="{{ route('announcements.create') }}" class="btn btn-primary">Dodaj zlecenie</a>
                    @endif
                @else
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary">Dodaj zlecenie</a>
                @endauth
            </div>
        </div>
    </div>

    <div class="container-page py-8">
        {{-- Filtry --}}
        <div class="rounded-xl border border-ink-100 bg-white p-4 shadow-sm" x-data="{ showAdvanced: @js(filled($minBudget) || filled($maxBudget)) }">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="relative md:col-span-6">
                    <label for="search" class="sr-only">Szukaj zleceń</label>
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                    <input id="search" type="search" wire:model.live.debounce.300ms="search" placeholder="Szukaj po tytule lub opisie…" class="input pl-10">
                </div>

                <div class="md:col-span-3">
                    <label for="category" class="sr-only">Kategoria</label>
                    <select id="category" wire:model.live="category" class="input">
                        <option value="">Wszystkie kategorie</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 md:col-span-3">
                    <button type="button" @click="showAdvanced = !showAdvanced" :aria-expanded="showAdvanced"
                            class="btn btn-secondary flex-1" :class="showAdvanced && 'ring-brand-300 bg-brand-50 text-brand-700'">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 3.75a2 2 0 1 0-4 0 2 2 0 0 0 4 0ZM17.25 4.5a.75.75 0 0 0 0-1.5h-5.5a.75.75 0 0 0 0 1.5h5.5ZM5 3.75a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 .75.75ZM4.25 17a.75.75 0 0 0 0-1.5h-1.5a.75.75 0 0 0 0 1.5h1.5ZM17.25 17a.75.75 0 0 0 0-1.5h-5.5a.75.75 0 0 0 0 1.5h5.5ZM9 10a.75.75 0 0 1-.75.75h-5.5a.75.75 0 0 1 0-1.5h5.5A.75.75 0 0 1 9 10ZM17.25 10.75a.75.75 0 0 0 0-1.5h-1.5a.75.75 0 0 0 0 1.5h1.5ZM14 10a2 2 0 1 0-4 0 2 2 0 0 0 4 0ZM10 16.25a2 2 0 1 0-4 0 2 2 0 0 0 4 0Z"/></svg>
                        Budżet
                    </button>
                    @if(filled($search) || filled($category) || filled($minBudget) || filled($maxBudget))
                        <button wire:click="clearFilters" type="button" class="btn btn-ghost" title="Wyczyść filtry">
                            Wyczyść
                        </button>
                    @endif
                </div>
            </div>

            <div x-show="showAdvanced" x-collapse x-cloak>
                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-ink-100 pt-4 sm:grid-cols-2 md:max-w-xl">
                    <div>
                        <label for="minBudget" class="label">Budżet od (PLN)</label>
                        <input id="minBudget" type="number" min="0" step="100" wire:model.live.debounce.500ms="minBudget" placeholder="np. 1000" class="input">
                    </div>
                    <div>
                        <label for="maxBudget" class="label">Budżet do (PLN)</label>
                        <input id="maxBudget" type="number" min="0" step="100" wire:model.live.debounce.500ms="maxBudget" placeholder="np. 10 000" class="input">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between text-sm text-ink-500">
            <a href="{{ route('search.advanced') }}" class="font-medium text-brand-600 hover:text-brand-700">Wyszukiwanie zaawansowane &rarr;</a>
        </div>

        {{-- Wyniki: szkielet podczas ładowania zamiast znikającej siatki --}}
        <div wire:loading.delay class="mt-6 grid w-full grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3" aria-hidden="true">
            @for($i = 0; $i < 6; $i++)
                <div class="h-64 animate-pulse rounded-xl border border-ink-100 bg-white p-5">
                    <div class="h-4 w-24 rounded bg-ink-100"></div>
                    <div class="mt-5 h-5 w-3/4 rounded bg-ink-100"></div>
                    <div class="mt-3 h-3 w-full rounded bg-ink-50"></div>
                    <div class="mt-2 h-3 w-5/6 rounded bg-ink-50"></div>
                    <div class="mt-10 h-8 w-full rounded bg-ink-50"></div>
                </div>
            @endfor
        </div>

        <div wire:loading.delay.remove class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($announcements as $announcement)
                <x-announcement-card :announcement="$announcement" wire:key="a-{{ $announcement->id }}" />
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-ink-200 bg-white px-6 py-16 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-ink-50 text-ink-400">
                        <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                    </span>
                    <h3 class="mt-4 font-semibold">Brak zleceń dla wybranych filtrów</h3>
                    <p class="mt-1 text-sm text-ink-500">Spróbuj innej frazy, kategorii albo szerszego zakresu budżetu.</p>
                    <button wire:click="clearFilters" type="button" class="btn btn-secondary mt-6">Wyczyść filtry</button>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $announcements->links() }}
        </div>
    </div>

{{-- Schema.org: lista ofert. Budujemy tablicę w PHP i kodujemy json_encode -
     wcześniejsze addslashes() dawało niepoprawny JSON (\' i surowe nowe linie). --}}
@if($announcements->count() > 0)
@push('head')
@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => $announcements->values()->map(function ($a, $index) {
            $location = trim((string) $a->location);
            $isRemote = $location === '' || in_array(mb_strtolower($location), ['zdalna', 'zdalnie'], true);
            try {
                $validThrough = $a->deadline ? \Carbon\Carbon::parse($a->deadline) : $a->created_at->copy()->addDays(30);
            } catch (\Throwable $e) {
                $validThrough = $a->created_at->copy()->addDays(30);
            }

            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'JobPosting',
                    'title' => $a->title,
                    'description' => \Illuminate\Support\Str::limit(strip_tags($a->description), 200),
                    'datePosted' => $a->created_at->toIso8601String(),
                    'validThrough' => $validThrough->toIso8601String(),
                    'url' => route('announcements.show', $a),
                    'hiringOrganization' => [
                        '@type' => 'Organization',
                        'name' => $a->user->company ?: $a->user->name,
                    ],
                    'jobLocation' => [
                        '@type' => 'Place',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => $isRemote ? 'Zdalna' : $location,
                            'addressCountry' => 'PL',
                        ],
                    ],
                    'baseSalary' => [
                        '@type' => 'MonetaryAmount',
                        'currency' => $a->budget_currency ?: 'PLN',
                        'value' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => (float) ($a->budget_min ?? 0),
                            'maxValue' => (float) ($a->budget_max ?? $a->budget_min ?? 0),
                            'unitText' => 'TOTAL',
                        ],
                    ],
                ],
            ];
        })->all(),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush
@endif
</div>
