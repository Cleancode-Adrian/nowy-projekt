@props(['page'])

@php
    $hasChildren = $page->children()->count() > 0;
    $url = $page->url;
@endphp

@if($hasChildren)
    <div class="relative group" x-data="{ open: false }">
        <a href="{{ $url }}" 
           @mouseenter="open = true" 
           @mouseleave="open = false"
           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1">
            @if($page->icon)
                <i class="{{ $page->icon }}"></i>
            @endif
            {{ $page->title }}
            <i class="fa-solid fa-chevron-down text-xs"></i>
        </a>
        
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @mouseenter="open = true"
             @mouseleave="open = false"
             class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
             style="display: none;">
            @foreach($page->children as $child)
                <a href="{{ $child->url }}" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                    @if($child->icon)
                        <i class="{{ $child->icon }} mr-2"></i>
                    @endif
                    {{ $child->title }}
                </a>
            @endforeach
        </div>
    </div>
@else
    <a href="{{ $url }}" 
       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-1">
        @if($page->icon)
            <i class="{{ $page->icon }}"></i>
        @endif
        {{ $page->title }}
    </a>
@endif

