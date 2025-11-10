<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <article class="card mb-8">
            @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}"
                     alt="{{ $post->title }}"
                     class="w-full h-96 object-cover rounded-xl -mt-6 -mx-6 mb-8">
            @endif

            <div class="flex items-center gap-2 mb-4">
                @foreach($post->tags as $tag)
                    <span class="bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full">{{ $tag->name }}</span>
                @endforeach
            </div>

            <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            <div class="flex items-center gap-6 text-sm text-gray-600 mb-8 pb-6 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    @if($post->author->avatar)
                        <img src="{{ asset('storage/' . $post->author->avatar) }}"
                             class="w-10 h-10 rounded-full">
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900">{{ $post->author->name }}</p>
                        <p class="text-xs">{{ $post->published_at->format('d.m.Y') }}</p>
                    </div>
                </div>
                <span>📖 {{ $post->reading_time }} min czytania</span>
                <span>👁️ {{ $post->views_count }} wyświetleń</span>
            </div>

            <div class="prose prose-lg max-w-none">
                {!! $post->content !!}
            </div>
        </article>

        @if($relatedPosts->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">📚 Podobne artykuły</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $related)
                        <a href="{{ route('blog.show', $related->slug) }}" class="card hover:shadow-lg transition-shadow">
                            @if($related->featured_image)
                                <img src="{{ asset('storage/' . $related->featured_image) }}"
                                     class="w-full h-32 object-cover rounded-lg mb-3">
                            @endif
                            <h3 class="font-bold text-gray-900 mb-2">{{ $related->title }}</h3>
                            <p class="text-xs text-gray-500">{{ $related->published_at->format('d.m.Y') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

