<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header / Profile Card --}}
        <div class="card mb-8">
            <div class="flex flex-col md:flex-row items-start gap-6">

                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                             class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                    @else
                        <div class="w-32 h-32 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-5xl font-bold border-4 border-gray-200">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                                {{ $user->name }}
                                @if($user->is_verified)
                                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold" title="Zweryfikowany">
                                        ✓ Zweryfikowany
                                    </span>
                                @endif
                            </h1>
                            <div class="flex items-center gap-4 text-gray-600 mb-3">
                                <span class="font-semibold">{{ $user->isFreelancer() ? '💼 Freelancer' : '👤 Klient' }}</span>
                                @if($user->experience_level && $user->isFreelancer())
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">
                                        {{ ucfirst($user->experience_level) }}
                                    </span>
                                @endif
                                @if($user->company)
                                    <span>🏢 {{ $user->company }}</span>
                                @endif
                            </div>

                            @if($user->average_rating > 0)
                                <div class="flex items-center gap-3 mb-3">
                                    <x-star-rating :rating="$user->average_rating" size="md" />
                                    <span class="text-lg font-semibold text-gray-900">
                                        {{ number_format($user->average_rating, 1) }}
                                    </span>
                                    <a href="{{ route('users.ratings', $user) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                                        ({{ $user->ratings_count }} {{ $user->ratings_count === 1 ? 'ocena' : 'ocen' }})
                                    </a>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div class="mb-3">
                                <x-user-badges :user="$user" size="md" />
                            </div>
                        </div>

                        @if(auth()->check() && auth()->id() !== $user->id)
                            <a href="{{ route('messages.show', $user) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-envelope"></i>
                                Napisz wiadomość
                            </a>
                        @endif
                    </div>

                    @if($user->bio)
                        <p class="text-gray-700 mb-4 leading-relaxed">{{ $user->bio }}</p>
                    @endif

                    {{-- Skills --}}
                    @if($user->skills && count($user->skills) > 0)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Umiejętności:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->skills as $skill)
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Links --}}
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                        @if($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
                                <i class="fa-brands fa-linkedin"></i> LinkedIn
                            </a>
                        @endif
                        @if($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank"
                               class="text-gray-900 hover:text-gray-700 text-sm font-medium flex items-center gap-1">
                                <i class="fa-brands fa-github"></i> GitHub
                            </a>
                        @endif
                        @if($user->website)
                            <a href="{{ $user->website }}" target="_blank"
                               class="text-gray-600 hover:text-gray-900 text-sm font-medium flex items-center gap-1">
                                <i class="fa-solid fa-globe"></i> Strona WWW
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['projects'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Ukończone projekty</div>
            </div>
            <div class="card text-center">
                <div class="text-3xl font-bold text-yellow-500">{{ number_format($stats['rating'], 1) }}</div>
                <div class="text-sm text-gray-600 mt-1">Średnia ocena</div>
            </div>
            <div class="card text-center">
                <div class="text-3xl font-bold text-green-600">{{ $stats['reviews'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Opinie</div>
            </div>
            <div class="card text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['views'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Wyświetlenia profilu</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Portfolio (dla freelancerów) --}}
            @if($user->isFreelancer() && $portfolioItems->count() > 0)
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">💼 Portfolio</h2>
                        @if($user->portfolioItems()->count() > 6)
                            <a href="{{ route('portfolio.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Zobacz wszystkie →
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($portfolioItems as $item)
                            <div class="card">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}"
                                         class="w-full h-40 object-cover rounded-lg mb-4">
                                @else
                                    <div class="w-full h-40 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg mb-4 flex items-center justify-center text-white text-4xl">
                                        💼
                                    </div>
                                @endif
                                <h3 class="font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $item->description }}</p>
                                @if($item->technologies)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item->technologies as $tech)
                                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">{{ $tech }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent Reviews --}}
            <div class="{{ $user->isFreelancer() && $portfolioItems->count() > 0 ? 'lg:col-span-1' : 'lg:col-span-3' }}">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">⭐ Ostatnie opinie</h2>
                    @if($ratings->count() > 0 && $user->ratings_count > 5)
                        <a href="{{ route('users.ratings', $user) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Zobacz wszystkie ({{ $user->ratings_count }}) →
                        </a>
                    @endif
                </div>

                @forelse($ratings as $rating)
                    <div class="card mb-4">
                        <div class="flex items-start gap-3 mb-2">
                            @if($rating->rater->avatar)
                                <img src="{{ asset('storage/' . $rating->rater->avatar) }}"
                                     alt="{{ $rating->rater->name }}"
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($rating->rater->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $rating->rater->name }}</div>
                                <x-star-rating :rating="$rating->rating" size="sm" />
                            </div>
                            <span class="text-xs text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                        </div>
                        @if($rating->comment)
                            <p class="text-sm text-gray-700">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <div class="text-5xl mb-3">⭐</div>
                        <p class="text-gray-500 font-medium">Ten użytkownik nie ma jeszcze opinii</p>
                        <p class="text-gray-400 text-sm mt-2">Statystyka pokazuje łączną liczbę otrzymanych ocen</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

