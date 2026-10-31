@props(['announcement'])

@php
    $category = $announcement->category;
    $color = $category->color ?? '#2f48e0';
    $currency = $announcement->budget_currency ?: 'PLN';
    $fmt = fn ($v) => number_format((float) $v, 0, ',', ' ');

    if ($announcement->budget_min && $announcement->budget_max) {
        $budget = $fmt($announcement->budget_min) . ' – ' . $fmt($announcement->budget_max) . ' ' . $currency;
    } elseif ($announcement->budget_max) {
        $budget = 'do ' . $fmt($announcement->budget_max) . ' ' . $currency;
    } elseif ($announcement->budget_min) {
        $budget = 'od ' . $fmt($announcement->budget_min) . ' ' . $currency;
    } else {
        $budget = 'Do uzgodnienia';
    }

    $deadline = null;
    if (!empty($announcement->deadline)) {
        try {
            $deadline = \Illuminate\Support\Carbon::parse($announcement->deadline)->translatedFormat('j M Y');
        } catch (\Throwable $e) {
            $deadline = $announcement->deadline;
        }
    }

    $author = $announcement->user;
    $url = route('announcements.show', $announcement);

    // Zamknięte = zleceniodawca wybrał wykonawcę; po terminie = minął deadline, ale nikogo nie wybrano.
    $isClosed = $announcement->status === 'closed';
    $isExpired = false;
    if (!$isClosed && !empty($announcement->deadline)) {
        try {
            $isExpired = \Illuminate\Support\Carbon::parse($announcement->deadline)->endOfDay()->isPast();
        } catch (\Throwable $e) {
            $isExpired = false;
        }
    }
@endphp

<article {{ $attributes->class([
    'group relative flex h-full flex-col rounded-xl border p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg',
    'border-ink-100 bg-white hover:border-ink-200' => ! $isClosed,
    'border-ink-200 border-dashed bg-ink-50/70 hover:border-ink-300' => $isClosed,
]) }}>
    {{-- Kategoria, status, data --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-2">
            <span class="inline-flex min-w-0 items-center gap-1.5 rounded-md bg-ink-50 px-2 py-1 text-xs font-medium text-ink-700">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background-color: {{ $color }}"></span>
                <span class="truncate">{{ $category->name ?? 'Bez kategorii' }}</span>
            </span>
            @if($isClosed)
                <span class="badge bg-ink-800 text-white" title="Zleceniodawca wybrał już wykonawcę">
                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
                    Zamknięte
                </span>
            @elseif($isExpired)
                <span class="badge badge-warning" title="Termin realizacji minął">Po terminie</span>
            @elseif($announcement->is_urgent)
                <span class="badge badge-danger">Pilne</span>
            @endif
        </div>
        <time datetime="{{ $announcement->created_at->toIso8601String() }}" class="shrink-0 text-xs text-ink-400">
            {{ $announcement->created_at->diffForHumans() }}
        </time>
    </div>

    {{-- Tytuł (cała karta klikalna przez ::after) --}}
    <h3 @class(['mt-4 text-base font-semibold leading-snug group-hover:text-brand-700', 'text-ink-900' => ! $isClosed, 'text-ink-500' => $isClosed])>
        <a href="{{ $url }}" class="line-clamp-2 after:absolute after:inset-0 after:rounded-xl focus-visible:outline-none">
            {{ $announcement->title }}
        </a>
    </h3>

    <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-ink-500">
        {{ $announcement->description }}
    </p>

    {{-- Tagi --}}
    @if($announcement->tags->isNotEmpty())
        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach($announcement->tags->take(4) as $tag)
                <span class="rounded-md border border-ink-100 px-2 py-0.5 text-xs text-ink-600">{{ $tag->name }}</span>
            @endforeach
            @if($announcement->tags->count() > 4)
                <span class="px-1 py-0.5 text-xs text-ink-400">+{{ $announcement->tags->count() - 4 }}</span>
            @endif
        </div>
    @endif

    {{-- Budżet i termin --}}
    <dl class="mt-auto grid grid-cols-2 gap-3 pt-5">
        <div>
            <dt class="text-xs text-ink-400">Budżet</dt>
            <dd class="mt-0.5 text-sm font-semibold text-ink-900">{{ $budget }}</dd>
        </div>
        <div>
            <dt class="text-xs text-ink-400">Termin</dt>
            <dd class="mt-0.5 text-sm font-medium text-ink-700">{{ $deadline ?? 'Elastyczny' }}</dd>
        </div>
    </dl>

    {{-- Autor + akcje (z-10, żeby były klikalne ponad linkiem karty) --}}
    <div class="mt-4 flex items-center justify-between gap-3 border-t border-ink-100 pt-4">
        <div class="flex min-w-0 items-center gap-2">
            @if($author?->avatar)
                <img src="{{ asset('storage/' . $author->avatar) }}" alt="" class="h-7 w-7 rounded-full object-cover ring-1 ring-ink-100">
            @else
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-ink-100 text-xs font-semibold text-ink-600">
                    {{ mb_strtoupper(mb_substr($author->name ?? '?', 0, 1)) }}
                </span>
            @endif
            <span class="truncate text-sm text-ink-600">{{ $author->name ?? 'Użytkownik' }}</span>
            @if($author?->is_verified)
                <svg class="h-4 w-4 shrink-0 text-brand-600" viewBox="0 0 20 20" fill="currentColor" aria-label="Zweryfikowany"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 0-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
            @endif
        </div>

        <div class="relative z-10 flex shrink-0 items-center gap-2">
            @auth
                <livewire:save-button :announcementId="$announcement->id" :key="'save-'.$announcement->id" />
            @endauth
            <span class="hidden text-sm font-medium text-brand-600 sm:inline-flex sm:items-center sm:gap-1" aria-hidden="true">
                {{ $isClosed ? 'Zobacz' : 'Szczegóły' }}
                <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
            </span>
        </div>
    </div>
</article>
