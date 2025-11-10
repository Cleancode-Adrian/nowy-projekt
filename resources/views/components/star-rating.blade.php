@props(['rating' => 0, 'showNumber' => false, 'size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-lg',
        'lg' => 'text-2xl',
    ];
    $textSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="flex items-center gap-1">
    @for ($i = 1; $i <= 5; $i++)
        <span class="{{ $textSize }} {{ $i <= round($rating) ? 'text-yellow-400' : 'text-gray-300' }}">
            {{ $i <= round($rating) ? '★' : '☆' }}
        </span>
    @endfor

    @if($showNumber)
        <span class="text-sm text-gray-600 ml-2">
            {{ number_format($rating, 1) }}
        </span>
    @endif
</div>

