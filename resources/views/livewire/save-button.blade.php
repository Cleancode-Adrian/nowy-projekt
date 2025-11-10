<button
    wire:click="toggle"
    class="border border-gray-300 hover:border-gray-400 text-gray-700 rounded-lg font-medium transition-colors {{ $full ? 'w-full py-3 px-4' : 'px-3 py-2 text-sm' }} {{ $isSaved ? 'bg-yellow-50 border-yellow-400' : '' }}">
    <span class="mr-2">{{ $isSaved ? '⭐' : '🔖' }}</span>
    @if($full)
        {{ $isSaved ? 'Zapisane' : 'Zapisz projekt' }}
    @endif
</button>

