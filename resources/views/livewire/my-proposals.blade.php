<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">💼 Moje oferty</h1>
            <p class="text-gray-600">Przeglądaj status wszystkich wysłanych propozycji</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Wszystkie</span>
                    <span class="text-2xl">📊</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Oczekujące</span>
                    <span class="text-2xl">⏳</span>
                </div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Zaakceptowane</span>
                    <span class="text-2xl">✅</span>
                </div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['accepted'] }}</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Odrzucone</span>
                    <span class="text-2xl">❌</span>
                </div>
                <div class="text-3xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
            </div>
        </div>

        {{-- Proposals List --}}
        <div class="card">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Lista ofert</h2>

            @forelse($proposals as $proposal)
                <div class="border border-gray-200 rounded-lg p-6 mb-4 {{ $proposal->status === 'accepted' ? 'bg-green-50 border-green-300' : '' }}">

                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <a href="{{ route('announcements.show', $proposal->announcement) }}"
                               class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                {{ $proposal->announcement->title }}
                            </a>
                            <div class="flex items-center gap-3 mt-2 text-sm text-gray-600">
                                <span class="px-2 py-1 rounded text-xs font-medium"
                                      style="background-color: {{ $proposal->announcement->category->color }}20; color: {{ $proposal->announcement->category->color }}">
                                    {{ $proposal->announcement->category->name }}
                                </span>
                                <span>Zleceniodawca: {{ $proposal->announcement->user->name }}</span>
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        @if($proposal->status === 'accepted')
                            <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                                ✅ Zaakceptowana
                            </span>
                        @elseif($proposal->status === 'rejected')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                                ❌ Odrzucona
                            </span>
                        @elseif($proposal->status === 'withdrawn')
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                ↩️ Wycofana
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                                ⏳ Oczekuje
                            </span>
                        @endif
                    </div>

                    {{-- Proposal Details --}}
                    <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-xs text-gray-600">Twoja cena</div>
                            <div class="text-xl font-bold text-green-600">{{ number_format($proposal->price, 2) }} PLN</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Twój termin</div>
                            <div class="text-xl font-bold text-blue-600">{{ $proposal->delivery_days }} dni</div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4 p-3 bg-white rounded-lg">
                        <p class="text-gray-700 text-sm">{{ Str::limit($proposal->description, 150) }}</p>
                    </div>

                    {{-- Actions & Date --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-xs text-gray-500">
                            Wysłana {{ $proposal->created_at->diffForHumans() }}
                        </span>
                        <div class="flex items-center gap-3">
                            @if($proposal->status === 'pending')
                                <button wire:click="withdraw({{ $proposal->id }})"
                                        wire:confirm="Czy na pewno chcesz wycofać tę ofertę?"
                                        class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                    ↩️ Wycofaj ofertę
                                </button>
                            @endif
                            <a href="{{ route('announcements.show', $proposal->announcement) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                👁️ Zobacz ogłoszenie
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-gray-50 rounded-lg">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak wysłanych ofert</h3>
                    <p class="text-gray-600 mb-6">Przeglądaj ogłoszenia i wyślij swoje pierwsze oferty!</p>
                    <a href="{{ route('announcements.index') }}" class="inline-flex items-center btn btn-primary">
                        📋 Przeglądaj ogłoszenia
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>

