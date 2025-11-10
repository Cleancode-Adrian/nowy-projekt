<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🔔 Powiadomienia</h1>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Oznacz wszystkie jako przeczytane
                </button>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="card {{ $notification->is_read ? 'bg-white' : 'bg-blue-50 border-blue-200' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $notification->title }}</h3>
                            <p class="text-gray-700 text-sm mb-2">{{ $notification->content }}</p>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                @if($notification->link)
                                    <a href="{{ $notification->link }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                        Zobacz →
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if(!$notification->is_read)
                            <button wire:click="markAsRead({{ $notification->id }})"
                                    class="text-gray-400 hover:text-gray-600 ml-4">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-xl">
                    <div class="text-6xl mb-4">🔔</div>
                    <p class="text-gray-500">Brak powiadomień</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

