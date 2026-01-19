<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                ← Powrót do panelu
            </a>
        </div>

        {{-- Announcement Info --}}
        <div class="card mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>

            <div class="flex items-center gap-6 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-semibold">Kategoria:</span>
                    <span class="px-2 py-1 rounded text-xs font-medium"
                          style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                        {{ $announcement->category->name }}
                    </span>
                </div>

                @if($announcement->budget_min && $announcement->budget_max)
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Budżet:</span>
                        <span>{{ number_format($announcement->budget_min, 0) }} - {{ number_format($announcement->budget_max, 0) }} PLN</span>
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    <span class="font-semibold">Wyświetlenia:</span>
                    <span>{{ $announcement->views_count }}</span>
                </div>
            </div>
        </div>

        {{-- Proposals List --}}
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        📨 Otrzymane oferty
                    </h2>
                    @if(auth()->user()->role === 'admin' && $announcement->user_id !== auth()->id())
                        <p class="text-sm text-gray-500 mt-1">Widzisz oferty jako administrator</p>
                    @endif
                </div>
                <span class="text-lg font-semibold text-blue-600">
                    {{ $proposals->count() }} {{ $proposals->count() === 1 ? 'oferta' : 'ofert' }}
                </span>
            </div>

            <div class="space-y-4">
                @forelse($proposals as $proposal)
                    <div class="border border-gray-200 rounded-lg p-6 transition-all {{ $proposal->status === 'accepted' ? 'bg-green-50 border-green-300 shadow-md' : 'hover:shadow-md' }}">

                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($proposal->freelancer->avatar)
                                    <img src="{{ asset('storage/' . $proposal->freelancer->avatar) }}"
                                         alt="{{ $proposal->freelancer->name }}"
                                         class="w-16 h-16 rounded-full mr-4 object-cover border-2 border-gray-200">
                                @else
                                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mr-4 text-white text-xl font-bold border-2 border-gray-200">
                                        {{ strtoupper(substr($proposal->freelancer->name, 0, 1)) }}
                                    </div>
                                @endif

                        <div>
                            <a href="{{ route('users.profile', $proposal->freelancer) }}" class="font-bold text-lg text-gray-900 hover:text-blue-600 transition-colors">
                                {{ $proposal->freelancer->name }}
                                @if($proposal->freelancer->is_verified)
                                    <span class="text-blue-500 text-sm">✓</span>
                                @endif
                            </a>

                            @if($proposal->freelancer->ratings_received_count > 0)
                                <div class="flex items-center gap-2 mt-1">
                                    <x-star-rating :rating="$proposal->freelancer->ratings_received_avg_rating" size="sm" />
                                    <span class="text-sm text-gray-600">
                                        ({{ $proposal->freelancer->ratings_received_count }} {{ $proposal->freelancer->ratings_received_count === 1 ? 'ocena' : 'ocen' }})
                                    </span>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Brak ocen</span>
                            @endif

                            @if($proposal->freelancer->bio)
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($proposal->freelancer->bio, 60) }}</p>
                            @endif
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
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                                    ⏳ Oczekuje
                                </span>
                            @endif
                        </div>

                        {{-- Proposal Details --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 p-4 sm:p-5 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-200">
                            <div class="text-center sm:text-left">
                                <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-1">Cena</div>
                                <div class="text-xl sm:text-2xl font-bold text-green-600">{{ number_format($proposal->price, 2) }} PLN</div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-1">Termin realizacji</div>
                                <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ $proposal->delivery_days }} {{ $proposal->delivery_days === 1 ? 'dzień' : 'dni' }}</div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-4 p-4 bg-white rounded-lg border border-gray-200">
                            <h5 class="font-semibold text-gray-900 mb-2">Opis oferty:</h5>
                            <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $proposal->description }}</p>
                        </div>

                        {{-- Actions --}}
                        @if($proposal->status === 'pending')
                            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                                @php
                                    $isOwner = $announcement->user_id === auth()->id();
                                    $isAdmin = auth()->user()->role === 'admin';
                                @endphp
                                @if($isOwner)
                                    <button
                                        wire:click="accept({{ $proposal->id }})"
                                        wire:confirm="Czy na pewno chcesz zaakceptować tę ofertę? Pozostałe oferty zostaną automatycznie odrzucone."
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-colors shadow-md hover:shadow-lg">
                                        ✅ Zaakceptuj ofertę
                                    </button>
                                    <button
                                        wire:click="reject({{ $proposal->id }})"
                                        wire:confirm="Czy na pewno chcesz odrzucić tę ofertę?"
                                        class="text-red-600 hover:text-red-700 font-semibold px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">
                                        ❌ Odrzuć
                                    </button>
                                @elseif($isAdmin)
                                    <span class="text-sm text-gray-500 italic bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-200">
                                        👁️ Tylko właściciel ogłoszenia może akceptować/odrzucać oferty
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="text-xs text-gray-500 mt-4 pt-3 border-t border-gray-100">
                            Wysłana {{ $proposal->created_at->diffForHumans() }} ({{ $proposal->created_at->format('d.m.Y H:i') }})
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-gray-50 rounded-lg">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak ofert</h3>
                        <p class="text-gray-600">Na razie nikt nie wysłał oferty do tego ogłoszenia</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

