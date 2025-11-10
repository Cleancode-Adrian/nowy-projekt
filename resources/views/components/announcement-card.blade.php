@props(['announcement'])

<div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow border border-gray-100 overflow-hidden">
    <div class="p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-medium px-3 py-1 rounded-full"
                  style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                {{ $announcement->category->name }}
            </span>
            <span class="text-sm text-gray-500">
                {{ $announcement->created_at->diffForHumans() }}
            </span>
        </div>

        {{-- Title --}}
        <h3 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
            {{ $announcement->title }}
        </h3>

        {{-- Description --}}
        <p class="text-gray-600 mb-4 line-clamp-3">
            {{ $announcement->description }}
        </p>

        {{-- Meta Info --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="text-green-500 mr-2">💰</span>
                    <span class="font-semibold text-gray-900">
                        {{ $announcement->budget_range ?? 'Do uzgodnienia' }}
                    </span>
                </div>
                @if($announcement->deadline)
                    <div class="flex items-center">
                        <span class="text-blue-500 mr-2">⏰</span>
                        <span class="text-sm text-gray-600">{{ $announcement->deadline }}</span>
                    </div>
                @endif
            </div>
            @if($announcement->is_urgent)
                <span class="text-red-500 text-xl" title="Projekt pilny">🔥</span>
            @endif
        </div>

        {{-- Tags --}}
        @if($announcement->tags->count() > 0)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($announcement->tags->take(4) as $tag)
                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        {{ $tag->name }}
                    </span>
                @endforeach
                @if($announcement->tags->count() > 4)
                    <span class="text-xs text-gray-500">+{{ $announcement->tags->count() - 4 }}</span>
                @endif
            </div>
        @endif

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mr-2 text-white text-xs font-bold">
                    {{ substr($announcement->user->name, 0, 1) }}
                </div>
                <span class="text-sm text-gray-600">{{ $announcement->user->name }}</span>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <livewire:save-button :announcementId="$announcement->id" :key="'save-'.$announcement->id" />
                @endauth

                <a href="{{ route('announcements.show', $announcement) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Zobacz szczegóły
                </a>
            </div>
        </div>
    </div>
</div>

