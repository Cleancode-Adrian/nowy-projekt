{{-- Jeden element główny - Livewire 3 wymaga pojedynczego roota;
     wcześniejszy osobny <style> poza <div> wywalał stronę błędem 500. --}}
<div>
    <div class="border-b border-ink-100 bg-white">
        <div class="container-page py-10">
            <nav class="text-sm text-ink-400" aria-label="Okruszki">
                <a href="{{ route('home') }}" class="hover:text-ink-700">Strona główna</a>
                <span class="mx-1.5">/</span>
                <span class="text-ink-600">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ $page->title }}</h1>
            @if($page->updated_at)
                <p class="mt-2 text-sm text-ink-400">Ostatnia aktualizacja: {{ $page->updated_at->translatedFormat('j F Y') }}</p>
            @endif
        </div>
    </div>

    <div class="container-page py-10 lg:py-14">
        <article class="prose prose-lg prose-ink mx-auto max-w-3xl rounded-2xl border border-ink-100 bg-white p-6 shadow-sm sm:p-10
                        prose-headings:scroll-mt-24 prose-h2:border-b prose-h2:border-ink-100 prose-h2:pb-2 prose-a:text-brand-600 prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl">
            {!! $page->safe_content !!}
        </article>
    </div>
</div>
