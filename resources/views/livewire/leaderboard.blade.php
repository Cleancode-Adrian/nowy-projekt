<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🏆 Ranking Freelancerów</h1>
            <p class="text-gray-600">Top 20 najlepszych specjalistów na platformie</p>
        </div>

        {{-- Filters --}}
        <div class="card mb-8">
            <div class="flex items-center gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mr-2">Sortuj według:</label>
                    <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="rating">Najwyżej oceniani</option>
                        <option value="projects">Najwięcej projektów</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Leaderboard --}}
        <div class="space-y-4">
            @foreach($topFreelancers as $index => $freelancer)
                <div class="card hover:shadow-lg transition-shadow {{ $index < 3 ? 'border-2' : '' }}
                            {{ $index === 0 ? 'border-yellow-400 bg-gradient-to-r from-yellow-50 to-orange-50' : '' }}
                            {{ $index === 1 ? 'border-gray-400 bg-gray-50' : '' }}
                            {{ $index === 2 ? 'border-orange-400 bg-orange-50' : '' }}">

                    <div class="flex items-center gap-6">
                        {{-- Rank --}}
                        <div class="flex-shrink-0 text-center">
                            @if($index === 0)
                                <div class="text-5xl">🥇</div>
                            @elseif($index === 1)
                                <div class="text-5xl">🥈</div>
                            @elseif($index === 2)
                                <div class="text-5xl">🥉</div>
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-gray-600">#{{ $index + 1 }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Avatar --}}
                        <a href="{{ route('users.profile', $freelancer) }}" class="flex-shrink-0">
                            @if($freelancer->avatar)
                                <img src="{{ asset('storage/' . $freelancer->avatar) }}"
                                     alt="{{ $freelancer->name }}"
                                     class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md hover:scale-105 transition-transform">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-md hover:scale-105 transition-transform">
                                    {{ strtoupper(substr($freelancer->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('users.profile', $freelancer) }}"
                                   class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                    {{ $freelancer->name }}
                                </a>
                                @if($freelancer->is_verified)
                                    <span class="text-blue-500 text-lg" title="Zweryfikowany">✓</span>
                                @endif
                                @if($freelancer->experience_level)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">
                                        {{ ucfirst($freelancer->experience_level) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Badges --}}
                            <x-user-badges :user="$freelancer" size="sm" />

                            {{-- Stats --}}
                            <div class="flex items-center gap-6 mt-2 text-sm text-gray-600">
                                @if($freelancer->average_rating > 0)
                                    <div class="flex items-center gap-1">
                                        <x-star-rating :rating="$freelancer->average_rating" size="sm" />
                                        <span class="font-semibold text-gray-900">{{ number_format($freelancer->average_rating, 1) }}</span>
                                        <span>({{ $freelancer->ratings_count }})</span>
                                    </div>
                                @endif
                                <div>💼 {{ $freelancer->completed_projects }} projektów</div>
                            </div>

                            @if($freelancer->bio)
                                <p class="text-sm text-gray-600 mt-2 line-clamp-1">{{ $freelancer->bio }}</p>
                            @endif
                        </div>

                        {{-- Action --}}
                        <div class="flex-shrink-0">
                            <a href="{{ route('users.profile', $freelancer) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                Zobacz profil
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($topFreelancers->count() === 0)
            <div class="text-center py-16 bg-white rounded-xl">
                <div class="text-6xl mb-4">🏆</div>
                <p class="text-gray-500">Brak freelancerów w rankingu</p>
            </div>
        @endif

    </div>
</div>

