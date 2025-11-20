<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('messages.index') }}" class="text-blue-600 hover:text-blue-700 mr-4">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>

                    @if($otherUser->avatar)
                        <img src="{{ asset('storage/' . $otherUser->avatar) }}"
                             alt="{{ $otherUser->name }}"
                             class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 mr-4">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-lg font-bold border-2 border-gray-200 mr-4">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                    @endif

                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $otherUser->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $otherUser->role === 'freelancer' ? '💼 Freelancer' : '👤 Klient' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Messages Container --}}
        <div class="bg-white shadow-sm" style="height: 500px; overflow-y: auto;" id="messages-container">
            <div class="p-6 space-y-4">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            @if($message->sender_id !== auth()->id())
                                <div class="flex items-start mb-1">
                                    <span class="text-xs text-gray-500">{{ $message->sender->name }}</span>
                                </div>
                            @endif

                            <div class="rounded-lg p-4 {{ $message->sender_id === auth()->id()
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-100 text-gray-900' }}">
                                <p class="text-sm whitespace-pre-wrap break-words">{{ $message->content }}</p>
                            </div>

                            <div class="flex items-center mt-1 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <span class="text-xs text-gray-500">
                                    {{ $message->created_at->format('H:i') }}
                                </span>
                                @if($message->sender_id === auth()->id() && $message->is_read)
                                    <span class="text-xs text-blue-600 ml-2">✓✓</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <p class="text-4xl mb-4">💬</p>
                        <p>Rozpocznij rozmowę wysyłając pierwszą wiadomość</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Message Input --}}
        <div class="bg-white rounded-b-xl shadow-sm border-t border-gray-200 p-4">
            <form wire:submit="sendMessage" class="flex gap-3">
                <div class="flex-1">
                    <textarea
                        wire:model="content"
                        rows="2"
                        placeholder="Wpisz wiadomość..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        @keydown.enter.prevent="$wire.sendMessage()"
                    ></textarea>
                    @error('content')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2 self-end h-fit"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fa-solid fa-paper-plane"></i>
                        Wyślij
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        Wysyłanie...
                    </span>
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2">
                Naciśnij Enter aby wysłać wiadomość
            </p>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Scroll to bottom on load and after sending message
    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Scroll on page load
    document.addEventListener('DOMContentLoaded', scrollToBottom);

    // Scroll after Livewire updates
    document.addEventListener('livewire:init', () => {
        Livewire.on('scroll-to-bottom', () => {
            setTimeout(scrollToBottom, 100);
        });
    });

    // Initial scroll
    setTimeout(scrollToBottom, 100);
</script>
@endpush

