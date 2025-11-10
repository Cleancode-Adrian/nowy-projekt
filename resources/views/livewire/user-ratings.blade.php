<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">⭐ Opinie o {{ $user->name }}</h1>

            @if($stats['total'] > 0)
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="text-5xl font-bold text-gray-900">{{ number_format($stats['average'], 1) }}</div>
                        <div>
                            <x-star-rating :rating="$stats['average']" size="md" />
                            <p class="text-sm text-gray-600 mt-1">{{ $stats['total'] }} {{ $stats['total'] === 1 ? 'ocena' : 'ocen' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Rating Distribution --}}
        @if($stats['total'] > 0)
            <div class="card mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Rozkład ocen</h3>
                <div class="space-y-2">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-700 w-8">{{ $star }} ⭐</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-3">
                                @php
                                    $percentage = $stats['total'] > 0 ? ($stats['distribution'][$star] / $stats['total'] * 100) : 0;
                                @endphp
                                <div class="bg-yellow-400 h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12 text-right">{{ $stats['distribution'][$star] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Ratings List --}}
        <div class="space-y-4">
            @forelse($ratings as $rating)
                <div class="card">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @if($rating->rater->avatar)
                                <img src="{{ asset('storage/' . $rating->rater->avatar) }}"
                                     alt="{{ $rating->rater->name }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-lg font-bold border-2 border-gray-200">
                                    {{ strtoupper(substr($rating->rater->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $rating->rater->name }}</h4>
                                <x-star-rating :rating="$rating->rating" size="sm" />
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                    </div>

                    @if($rating->comment)
                        <p class="text-gray-700 mb-3">{{ $rating->comment }}</p>
                    @endif

                    @if($rating->announcement)
                        <div class="text-xs text-gray-500 pt-3 border-t border-gray-100">
                            Projekt:
                            <a href="{{ route('announcements.show', $rating->announcement) }}"
                               class="text-blue-600 hover:text-blue-700 font-medium">
                                {{ $rating->announcement->title }}
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-xl">
                    <div class="text-6xl mb-4">⭐</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak opinii</h3>
                    <p class="text-gray-600">{{ $user->name }} nie ma jeszcze żadnych opinii</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

