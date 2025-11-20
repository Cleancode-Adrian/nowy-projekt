<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📝 Blog Projekciarz.pl</h1>
            <p class="text-gray-600">Porady, tutoriale i nowości ze świata freelancingu</p>
        </div>

        {{-- Search & Filters --}}
        <div class="card mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" wire:model.live.debounce.300ms="search"
                       class="input" placeholder="Szukaj artykułów...">
                <select wire:model.live="tag" class="input">
                    <option value="">Wszystkie kategorie</option>
                    @foreach($tags as $t)
                        <option value="{{ $t->slug }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Posts Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
            @forelse($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="block h-full">
                    <article class="card hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer h-full flex flex-col overflow-hidden">
                        <div class="w-full h-48 bg-gray-100 rounded-xl overflow-hidden mb-4">
                            @if($post->featured_image)
                                @if(str_starts_with($post->featured_image, 'http://') || str_starts_with($post->featured_image, 'https://'))
                                    <img src="{{ $post->featured_image }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center" style="display: none;">
                                        <i class="fa-solid fa-blog text-white text-6xl opacity-20"></i>
                                    </div>
                                @else
                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover">
                                @endif
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <i class="fa-solid fa-blog text-white text-6xl opacity-20"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                @foreach($post->tags as $tag)
                                    <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded">{{ $tag->name }}</span>
                                @endforeach
                            </div>

                            <h2 class="text-xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors">
                                {{ $post->title }}
                            </h2>

                            @if($post->excerpt)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                @if($post->author->avatar)
                                    <img src="{{ asset('storage/' . $post->author->avatar) }}"
                                         class="w-6 h-6 rounded-full">
                                @endif
                                <span>{{ $post->author->name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span>📅 {{ $post->published_at->format('d.m.Y') }}</span>
                                <span>👁️ {{ $post->views_count }}</span>
                            </div>
                        </div>
                    </article>
                </a>
            @empty
                <div class="col-span-3 text-center py-16">
                    <p class="text-gray-500">Brak artykułów</p>
                </div>
            @endforelse
        </div>

        {{ $posts->links() }}
    </div>
</div>

