<div>
{{-- Breadcrumb --}}
<section class="bg-white border-b border-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-4 text-sm">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">🏠 Strona główna</a>
            <span class="text-gray-400">›</span>
            <a href="{{ route('announcements.index') }}" class="text-gray-500 hover:text-gray-700">Ogłoszenia</a>
            <span class="text-gray-400">›</span>
            <span class="text-gray-900 font-medium">{{ $announcement->title }}</span>
        </nav>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2">

            {{-- Header --}}
            <div class="card mb-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center mb-4">
                            <span class="px-3 py-1 text-sm font-medium rounded-full mr-3"
                                  style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                                {{ $announcement->category->name }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Dodano {{ $announcement->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>

                        <div class="flex flex-wrap items-center gap-6 mb-6">
                            <div class="flex items-center">
                                <span class="text-green-500 text-xl mr-2">💰</span>
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ $announcement->budget_range ?? 'Do uzgodnienia' }}
                                </span>
                            </div>

                            @if($announcement->deadline)
                                <div class="flex items-center">
                                    <span class="text-blue-500 text-xl mr-2">⏰</span>
                                    <span class="text-gray-600">Termin: {{ $announcement->deadline }}</span>
                                </div>
                            @endif

                            @if($announcement->location)
                                <div class="flex items-center">
                                    <span class="text-red-500 text-xl mr-2">📍</span>
                                    <span class="text-gray-600">{{ $announcement->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($announcement->is_urgent)
                        <div class="flex items-center space-x-2 bg-red-50 px-4 py-2 rounded-lg border border-red-200">
                            <span class="text-xl">🔥</span>
                            <span class="text-sm font-semibold text-red-700">Projekt pilny</span>
                        </div>
                    @endif
                </div>

                {{-- Tags --}}
                @if($announcement->tags->count() > 0)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($announcement->tags as $tag)
                            <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="mr-2">👁️</span>
                        <span>{{ $announcement->views_count }} wyświetleń</span>
                    </div>
                    @if($announcement->proposals_count > 0)
                        <div class="text-sm text-gray-600">
                            💼 {{ $announcement->proposals_count }} ofert
                        </div>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div class="card mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Opis projektu</h2>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">
                    {{ $announcement->description }}
                </div>
            </div>

            {{-- Attachments --}}
            @if($announcement->attachments->count() > 0)
                <div class="card mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">📎 Załączniki ({{ $announcement->attachments->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($announcement->attachments as $attachment)
                            <a href="{{ asset('storage/' . $attachment->file_path) }}"
                               download="{{ $attachment->original_name }}"
                               class="flex items-center gap-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fa-solid {{ $attachment->icon }} text-3xl"></i>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $attachment->original_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $attachment->file_size_formatted }}</p>
                                </div>
                                <i class="fa-solid fa-download text-blue-600"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Proposals Section (tylko dla właściciela) --}}
            @if(auth()->check() && auth()->id() === $announcement->user_id && $announcement->proposals_count > 0)
                <livewire:proposals-list :announcementId="$announcement->id" />
            @endif

            {{-- CTA dla freelancerów --}}
            @if(auth()->check() && auth()->id() !== $announcement->user_id)
                <livewire:proposal-form :announcement="$announcement" />
            @elseif(!auth()->check())
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg p-8 text-white">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold mb-4">Zainteresował Cię ten projekt?</h3>
                        <p class="text-blue-100 mb-6">
                            Zaloguj się aby złożyć ofertę do zleceniodawcy
                        </p>
                        <a href="{{ route('login') }}" class="inline-flex items-center bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition-colors">
                            📧 Zaloguj się i złóż ofertę
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            {{-- Author Info --}}
            <div class="card sticky top-24">
                <div class="text-center mb-6">
                    @if($announcement->user->avatar)
                        <img src="{{ asset('storage/' . $announcement->user->avatar) }}"
                             alt="{{ $announcement->user->name }}"
                             class="w-16 h-16 rounded-full mx-auto mb-4 object-cover">
                    @else
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-2xl font-bold">
                            {{ substr($announcement->user->name, 0, 1) }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->user->name }}</h3>
                    @if($announcement->user->company)
                        <p class="text-sm text-gray-600 mt-1">{{ $announcement->user->company }}</p>
                    @endif

                    {{-- Rating --}}
                    @if($announcement->user->ratings_count > 0)
                        <div class="flex items-center justify-center mt-2">
                            <x-star-rating :rating="$announcement->user->average_rating" :showNumber="true" />
                            <span class="text-xs text-gray-500 ml-2">({{ $announcement->user->ratings_count }} ocen)</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-4 mb-6 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Członek od:</span>
                        <span class="font-medium">{{ $announcement->user->created_at->format('m.Y') }}</span>
                    </div>
                    @if($announcement->user->phone)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Telefon:</span>
                            <span class="font-medium">{{ $announcement->user->masked_phone }}</span>
                        </div>
                    @endif
                </div>

                @auth
                    @if(auth()->id() !== $announcement->user_id)
                        <div class="space-y-3">
                            <livewire:save-button :announcementId="$announcement->id" :full="true" />
                            {{-- TODO: Aktywuj gdy messages.show będzie gotowe
                            <a href="{{ route('messages.show', $announcement->user_id) }}"
                               class="block w-full border border-gray-300 hover:border-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium text-center transition-colors">
                                💬 Wyślij wiadomość
                            </a>
                            --}}
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
</div>

{{-- Schema.org for this announcement --}}
@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "JobPosting",
    "title": "{{ $announcement->title }}",
    "description": "{{ $announcement->description }}",
    "datePosted": "{{ $announcement->created_at->toIso8601String() }}",
    "hiringOrganization": {
        "@@type": "Organization",
        "name": "{{ $announcement->user->company ?? $announcement->user->name }}"
    },
    "jobLocation": {
        "@@type": "Place",
        "address": {
            "@@type": "PostalAddress",
            "addressLocality": "{{ $announcement->location ?? 'Zdalna' }}"
        }
    }
    @if($announcement->budget_min && $announcement->budget_max)
    ,"baseSalary": {
        "@@type": "MonetaryAmount",
        "currency": "PLN",
        "value": {
            "@@type": "QuantitativeValue",
            "minValue": {{ $announcement->budget_min }},
            "maxValue": {{ $announcement->budget_max }},
            "unitText": "TOTAL"
        }
    }
    @endif
}
</script>
@endpush

