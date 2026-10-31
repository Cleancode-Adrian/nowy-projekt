<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    aria-pressed="{{ $isSaved ? 'true' : 'false' }}"
    title="{{ $isSaved ? 'Usuń z zapisanych' : 'Zapisz projekt' }}"
    @class([
        'inline-flex items-center justify-center gap-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-60',
        'w-full px-4 py-3' => $full,
        'h-9 w-9' => ! $full,
        'bg-brand-50 text-brand-700 ring-1 ring-brand-200 ring-inset hover:bg-brand-100' => $isSaved,
        'bg-white text-ink-500 ring-1 ring-ink-200 ring-inset hover:bg-ink-50 hover:text-ink-800' => ! $isSaved,
    ])>
    <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="{{ $isSaved ? 'currentColor' : 'none' }}" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/></svg>
    @if($full)
        {{ $isSaved ? 'Zapisane' : 'Zapisz projekt' }}
    @else
        <span class="sr-only">{{ $isSaved ? 'Usuń z zapisanych' : 'Zapisz projekt' }}</span>
    @endif
</button>
