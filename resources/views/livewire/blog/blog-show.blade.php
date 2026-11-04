@php
    $isExternalImage = $post->featured_image && preg_match('#^https?://#i', $post->featured_image);
    $imageUrl = $post->featured_image ? ($isExternalImage ? $post->featured_image : asset('storage/' . $post->featured_image)) : null;
    $toc = $post->table_of_contents;
    $tldr = $post->tldr_items;
    $updatedAt = $post->display_updated_at;

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'description' => $post->meta_description ?: $post->excerpt,
        'datePublished' => $post->published_at->toAtomString(),
        'dateModified' => ($updatedAt ?? $post->published_at)->toAtomString(),
        'author' => ['@type' => 'Person', 'name' => $post->author->name],
        'publisher' => ['@type' => 'Organization', 'name' => 'Projekciarz.pl', 'url' => url('/')],
        'mainEntityOfPage' => route('blog.show', $post->slug),
    ];
    if ($imageUrl) {
        $jsonLd['image'] = $imageUrl;
    }
@endphp

<div class="bg-ink-50/60">
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

    <div class="container-page py-8 lg:py-12">
        <nav class="mb-6 text-sm text-ink-500" aria-label="Okruszki">
            <a href="{{ route('home') }}" class="hover:text-ink-900">Strona główna</a>
            <span class="mx-2 text-ink-300">/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-ink-900">Blog</a>
            @if($post->category)
                <span class="mx-2 text-ink-300">/</span>
                <span class="text-ink-700">{{ $post->category->name }}</span>
            @endif
        </nav>

        <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_18rem] lg:gap-10 xl:grid-cols-[minmax(0,1fr)_20rem]">

            {{-- Artykuł --}}
            <article class="min-w-0">
                <header>
                    @if($post->tags->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <span class="badge badge-brand">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <h1 class="text-3xl font-bold tracking-tight text-ink-900 sm:text-4xl">{{ $post->title }}</h1>

                    @if($post->excerpt)
                        <p class="mt-4 text-lg leading-relaxed text-ink-600">{{ $post->excerpt }}</p>
                    @endif

                    {{-- Autor + daty --}}
                    <div class="mt-6 flex flex-col gap-4 border-y border-ink-100 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            @if($post->author->avatar)
                                <img src="{{ asset('storage/' . $post->author->avatar) }}" alt="" class="h-10 w-10 rounded-full object-cover ring-1 ring-ink-200">
                            @else
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-900 text-sm font-semibold text-white">{{ mb_strtoupper(mb_substr($post->author->name, 0, 1)) }}</span>
                            @endif
                            <div>
                                <p class="font-semibold text-ink-900">{{ $post->author->name }}</p>
                                <p class="text-ink-500">
                                    Opublikowano <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d.m.Y') }}</time>
                                    @if($updatedAt)
                                        <span class="mx-1.5 text-ink-300">·</span>
                                        Zaktualizowano <time datetime="{{ $updatedAt->toDateString() }}">{{ $updatedAt->format('d.m.Y') }}</time>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <dl class="flex items-center gap-5 text-ink-500">
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <dt class="sr-only">Czas czytania</dt>
                                <dd>{{ $post->reading_time }} min czytania</dd>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <dt class="sr-only">Wyświetlenia</dt>
                                <dd>{{ number_format($post->views_count, 0, ',', ' ') }} wyświetleń</dd>
                            </div>
                        </dl>
                    </div>
                </header>

                @if($imageUrl)
                    <figure class="mt-8 overflow-hidden rounded-2xl border border-ink-100 bg-ink-100">
                        <img src="{{ $imageUrl }}" alt="{{ $post->featured_image_alt ?: $post->title }}"
                             class="aspect-[1200/630] w-full object-cover" width="1200" height="630"
                             @if($isExternalImage) onerror="this.closest('figure').remove()" @endif>
                    </figure>
                @endif

                {{-- TL;DR --}}
                @if(count($tldr))
                    <aside class="mt-8 rounded-2xl border border-brand-100 bg-brand-50/60 p-5 sm:p-6" aria-labelledby="tldr-heading">
                        <p id="tldr-heading" class="eyebrow">TL;DR - w skrócie</p>
                        <ul class="mt-3 space-y-2 text-ink-800">
                            @foreach($tldr as $item)
                                <li class="flex gap-3">
                                    <svg class="mt-1 h-4 w-4 shrink-0 text-brand-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                @endif

                {{-- Spis treści (mobile) --}}
                @if(count($toc) > 1)
                    <details class="group mt-6 rounded-2xl border border-ink-100 bg-white lg:hidden">
                        <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 font-semibold text-ink-900">
                            Spis treści
                            <svg class="h-5 w-5 text-ink-400 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                        </summary>
                        <ol class="space-y-1 border-t border-ink-100 px-5 py-4 text-sm">
                            @foreach($toc as $item)
                                <li @class(['pl-4' => $item['level'] === 3])>
                                    <a href="#{{ $item['id'] }}" class="block py-1 text-ink-600 hover:text-brand-700">{{ $item['text'] }}</a>
                                </li>
                            @endforeach
                        </ol>
                    </details>
                @endif

                <div class="prose prose-lg mt-8 max-w-none scroll-mt-24 [&_h2]:scroll-mt-24 [&_h3]:scroll-mt-24">
                    {!! $post->content_with_anchors !!}
                </div>

                {{-- Zachęta: zleceniodawcom i gościom publikacja zlecenia, wykonawcom lista zleceń --}}
                <div class="mt-12 rounded-2xl bg-ink-900 p-6 text-white sm:p-8">
                    @if(auth()->user()?->isFreelancer())
                        <p class="text-sm font-medium text-ink-300">Szukasz nowych projektów?</p>
                        <p class="mt-1 text-xl font-semibold">Przejrzyj aktualne zlecenia i złóż ofertę</p>
                        <p class="mt-2 text-ink-300">Nowe projekty od zleceniodawców pojawiają się codziennie.</p>
                        <a href="{{ route('announcements.index') }}" class="btn btn-primary mt-5">Zobacz zlecenia</a>
                    @else
                        <p class="text-sm font-medium text-ink-300">Masz projekt do zrobienia?</p>
                        <p class="mt-1 text-xl font-semibold">Opisz zlecenie i porównaj oferty wykonawców</p>
                        <p class="mt-2 text-ink-300">Publikacja jest bezpłatna. Oferty przychodzą z portfolio i ocenami od poprzednich klientów.</p>
                        <a href="{{ route('announcements.create') }}" class="btn btn-primary mt-5">Dodaj zlecenie</a>
                    @endif
                </div>
            </article>

            {{-- Sidebar --}}
            <aside class="mt-10 lg:mt-0">
                <div class="space-y-6 lg:sticky lg:top-24">
                    @if(count($toc) > 1)
                        <nav class="hidden rounded-2xl border border-ink-100 bg-white p-5 lg:block" aria-labelledby="toc-heading"
                             x-data="{ active: '' }"
                             x-init="
                                const ids = {{ json_encode(array_column($toc, 'id')) }};
                                const headings = ids.map(id => document.getElementById(id)).filter(Boolean);
                                const io = new IntersectionObserver(entries => {
                                    entries.forEach(e => { if (e.isIntersecting) active = e.target.id; });
                                }, { rootMargin: '-96px 0px -70% 0px', threshold: 0 });
                                headings.forEach(h => io.observe(h));
                             ">
                            <p id="toc-heading" class="text-xs font-semibold uppercase tracking-wider text-ink-500">Spis treści</p>
                            <ol class="mt-3 space-y-0.5 text-sm">
                                @foreach($toc as $item)
                                    <li @class(['pl-3' => $item['level'] === 3])>
                                        <a href="#{{ $item['id'] }}"
                                           class="block rounded-md border-l-2 py-1.5 pl-3 pr-2 leading-snug transition-colors"
                                           :class="active === '{{ $item['id'] }}' ? 'border-brand-600 bg-brand-50 font-medium text-brand-700' : 'border-transparent text-ink-600 hover:bg-ink-50 hover:text-ink-900'">
                                            {{ $item['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif

                    @if($relatedPosts->count() > 0)
                        <div class="rounded-2xl border border-ink-100 bg-white p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-ink-500">Czytaj też</p>
                            <ul class="mt-3 divide-y divide-ink-100">
                                @foreach($relatedPosts as $related)
                                    <li class="py-3 first:pt-0 last:pb-0">
                                        <a href="{{ route('blog.show', $related->slug) }}" class="group block">
                                            <p class="font-medium leading-snug text-ink-900 group-hover:text-brand-700">{{ $related->title }}</p>
                                            <p class="mt-1 text-xs text-ink-500">{{ $related->published_at->format('d.m.Y') }} · {{ $related->reading_time }} min</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>
