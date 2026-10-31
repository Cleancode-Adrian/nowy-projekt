@props(['size' => 'md', 'dark' => false, 'wordmark' => true])

@php
    $box = ['sm' => 'h-7 w-7', 'md' => 'h-8 w-8', 'lg' => 'h-10 w-10'][$size] ?? 'h-8 w-8';
    $text = ['sm' => 'text-base', 'md' => 'text-lg', 'lg' => 'text-xl'][$size] ?? 'text-lg';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg class="{{ $box }} shrink-0" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <rect width="32" height="32" rx="8" class="{{ $dark ? 'fill-white' : 'fill-brand-600' }}"/>
        <path d="M11 24V8h6.2a5.3 5.3 0 0 1 0 10.6H11" class="{{ $dark ? 'stroke-ink-900' : 'stroke-white' }}" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="21.5" cy="23" r="1.9" class="{{ $dark ? 'fill-brand-600' : 'fill-brand-200' }}"/>
    </svg>
    @if($wordmark)
        <span class="{{ $text }} font-bold tracking-tight {{ $dark ? 'text-white' : 'text-ink-900' }}">
            Projekciarz<span class="{{ $dark ? 'text-brand-300' : 'text-brand-600' }}">.pl</span>
        </span>
    @endif
</span>
