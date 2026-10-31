@props(['page'])

@php
    // children są eager-loadowane w nagłówku - nie odpytujemy bazy ponownie
    $hasChildren = $page->relationLoaded('children') ? $page->children->isNotEmpty() : $page->children()->exists();
    $url = $page->url;
@endphp

@if($hasChildren)
    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false">
        <button type="button" @click="open = !open" :aria-expanded="open" class="nav-link inline-flex w-full items-center gap-1">
            @if($page->icon)
                <i class="{{ $page->icon }} text-ink-400"></i>
            @endif
            {{ $page->title }}
            <svg class="h-4 w-4 text-ink-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
        </button>

        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="z-50 mt-1 w-56 rounded-xl border border-ink-100 bg-white p-1.5 shadow-xl lg:absolute lg:left-0">
            <a href="{{ $url }}" class="dropdown-item font-medium">{{ $page->title }}</a>
            @foreach($page->children as $child)
                <a href="{{ $child->url }}" class="dropdown-item">
                    @if($child->icon)
                        <i class="{{ $child->icon }} w-4 text-ink-400"></i>
                    @endif
                    {{ $child->title }}
                </a>
            @endforeach
        </div>
    </div>
@else
    <a href="{{ $url }}" @class(['nav-link inline-flex items-center gap-1.5', 'nav-link-active' => url()->current() === $url])>
        @if($page->icon)
            <i class="{{ $page->icon }} text-ink-400"></i>
        @endif
        {{ $page->title }}
    </a>
@endif
