<div class="card">
    <h3 class="text-2xl font-bold text-gray-900 mb-6">
        📋 Otrzymane oferty ({{ $proposals->count() }})
    </h3>

    <div class="space-y-4">
        @forelse($proposals as $proposal)
            <div class="border border-gray-200 rounded-lg p-6 {{ $proposal->status === 'accepted' ? 'bg-green-50 border-green-300' : '' }}">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        @if($proposal->freelancer->avatar)
                            <img src="{{ asset('storage/' . $proposal->freelancer->avatar) }}"
                                 alt="{{ $proposal->freelancer->name }}"
                                 class="w-12 h-12 rounded-full mr-3 object-cover">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mr-3 text-white font-bold">
                                {{ substr($proposal->freelancer->name, 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $proposal->freelancer->name }}</h4>
                            @if($proposal->freelancer->ratings_count > 0)
                                <x-star-rating :rating="$proposal->freelancer->average_rating" size="sm" :showNumber="true" />
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    @if($proposal->status === 'accepted')
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <div class="text-center sm:text-left">
                        <div class="text-sm text-gray-600">Cena</div>
                        <div class="text-lg sm:text-xl font-bold text-green-600">{{ number_format($proposal->price, 2) }} PLN</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-sm text-gray-600">Termin</div>
                        <div class="text-lg sm:text-xl font-bold text-blue-600">{{ $proposal->delivery_days }} dni</div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $proposal->description }}</p>
                </div>

                {{-- Actions --}}
                @if($proposal->status === 'pending')
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                        @php
                            $canManage = $proposal->announcement->user_id === auth()->id();
                            $isAdmin = auth()->user()->role === 'admin';
                        @endphp
                        @if($canManage)
                            <button
                                wire:click="accept({{ $proposal->id }})"
                                wire:confirm="Zaakceptować tę ofertę? Pozostałe oferty zostaną odrzucone."
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                ✅ Zaakceptuj ofertę
                            </button>
                            <button
                                wire:click="reject({{ $proposal->id }})"
                                wire:confirm="Odrzucić tę ofertę?"
                                class="text-red-600 hover:text-red-700 font-medium">
                                ❌ Odrzuć
                            </button>
                        @elseif($isAdmin)
                            <span class="text-sm text-gray-500 italic">Tylko właściciel ogłoszenia może akceptować/odrzucać oferty</span>
                        @endif
                        <a href="{{ route('messages.show', $proposal->user_id) }}"
                           class="text-blue-600 hover:text-blue-700 font-medium {{ $canManage ? 'ml-auto' : '' }}">
                            💬 Wyślij wiadomość
                        </a>
                    </div>
                @endif

                <div class="text-xs text-gray-500 mt-3">
                    Wysłana {{ $proposal->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                Brak ofert do tego ogłoszenia
            </div>
        @endforelse
    </div>
</div>

