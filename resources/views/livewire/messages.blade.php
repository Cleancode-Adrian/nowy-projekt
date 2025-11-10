<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">💬 Wiadomości</h1>
                <p class="text-gray-600">Twoje prywatne rozmowy</p>
            </div>
            @if($unreadCount > 0)
                <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold">
                    {{ $unreadCount }} {{ $unreadCount === 1 ? 'nieprzeczytana' : 'nieprzeczytanych' }}
                </span>
            @endif
        </div>

        {{-- Conversations List --}}
        <div class="card">
            @forelse($conversations as $conversation)
                <a href="{{ route('messages.show', $conversation['user']->id) }}"
                   class="flex items-center p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">

                    {{-- Avatar --}}
                    <div class="flex-shrink-0 mr-4">
                        @if($conversation['user']->avatar)
                            <img src="{{ asset('storage/' . $conversation['user']->avatar) }}"
                                 alt="{{ $conversation['user']->name }}"
                                 class="w-14 h-14 rounded-full object-cover border-2 border-gray-200">
                        @else
                            <div class="w-14 h-14 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold border-2 border-gray-200">
                                {{ strtoupper(substr($conversation['user']->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- Message Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-semibold text-gray-900 {{ $conversation['unread_count'] > 0 ? 'font-bold' : '' }}">
                                {{ $conversation['user']->name }}
                            </h3>
                            <span class="text-xs text-gray-500">
                                {{ $conversation['latest_message']->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 truncate {{ $conversation['unread_count'] > 0 ? 'font-semibold text-gray-900' : '' }}">
                            @if($conversation['latest_message']->sender_id === auth()->id())
                                <span class="text-gray-400">Ty:</span>
                            @endif
                            {{ Str::limit($conversation['latest_message']->content, 60) }}
                        </p>
                    </div>

                    {{-- Unread Badge --}}
                    @if($conversation['unread_count'] > 0)
                        <div class="ml-4 bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                            {{ $conversation['unread_count'] }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">💬</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak wiadomości</h3>
                    <p class="text-gray-600 mb-6">
                        Rozpocznij rozmowę składając ofertę do ogłoszenia lub kontaktując się ze zleceniodawcą
                    </p>
                    <a href="{{ route('announcements.index') }}" class="inline-flex items-center btn btn-primary">
                        📋 Przeglądaj ogłoszenia
                    </a>
                </div>
            @endforelse
        </div>

        @if($conversations->count() > 0)
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                💡 <strong>Wskazówka:</strong> Kliknij na rozmowę aby otworzyć chat
            </div>
        @endif

    </div>
</div>

