@php
    $footerPages = \App\Models\Page::inMenu('footer')->with('children')->get();
@endphp

<footer class="bg-ink-950 text-ink-300" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Stopka</h2>

    <div class="container-page py-14 lg:py-16">
        <div class="grid grid-cols-2 gap-10 md:grid-cols-12">

            {{-- Marka --}}
            <div class="col-span-2 md:col-span-5">
                <a href="{{ route('home') }}" class="inline-block rounded-md" aria-label="Projekciarz.pl - strona główna">
                    <x-logo dark />
                </a>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-ink-400">
                    Platforma, na której zleceniodawcy publikują projekty stron, sklepów i aplikacji,
                    a zweryfikowani wykonawcy odpowiadają wyceną i terminem.
                </p>
                <ul class="mt-6 space-y-2 text-sm text-ink-400">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Publikacja zlecenia bez opłat
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                        Konta wykonawców zatwierdzane ręcznie
                    </li>
                </ul>
                <div class="mt-6 flex flex-col gap-2 text-sm">
                    <x-phone-link location="footer" class="font-semibold text-white transition-colors hover:text-brand-300" />
                    <a href="mailto:{{ config('app.contact.email') }}" class="inline-flex items-center gap-2 text-ink-400 transition-colors hover:text-white">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        {{ config('app.contact.email') }}
                    </a>
                </div>
            </div>

            {{-- Platforma --}}
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold text-white">Platforma</h3>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="{{ route('announcements.index') }}" class="transition-colors hover:text-white">Zlecenia</a></li>
                    <li><a href="{{ route('search.advanced') }}" class="transition-colors hover:text-white">Wyszukiwarka</a></li>
                    <li><a href="{{ route('leaderboard') }}" class="transition-colors hover:text-white">Ranking wykonawców</a></li>
                    <li><a href="{{ route('announcements.create') }}" class="transition-colors hover:text-white">Dodaj zlecenie</a></li>
                </ul>
            </div>

            {{-- Wiedza --}}
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold text-white">Wiedza</h3>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="{{ route('blog.index') }}" class="transition-colors hover:text-white">Blog</a></li>
                    <li><a href="{{ route('faq') }}" class="transition-colors hover:text-white">Częste pytania</a></li>
                    <li><a href="{{ route('home') }}#zakres-uslugi" class="transition-colors hover:text-white">Zakres usługi</a></li>
                </ul>
            </div>

            {{-- Informacje (strony z CMS) --}}
            <div class="md:col-span-3">
                <h3 class="text-sm font-semibold text-white">Informacje</h3>
                <ul class="mt-4 space-y-3 text-sm">
                    @forelse($footerPages as $page)
                        <li>
                            <a href="{{ $page->url }}" class="transition-colors hover:text-white">{{ $page->title }}</a>
                            @if($page->children->isNotEmpty())
                                <ul class="mt-2 space-y-2 border-l border-ink-800 pl-3">
                                    @foreach($page->children as $child)
                                        <li><a href="{{ $child->url }}" class="text-xs transition-colors hover:text-white">{{ $child->title }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @empty
                        <li><a href="{{ route('terms-of-service') }}" class="transition-colors hover:text-white">Regulamin</a></li>
                        <li><a href="{{ route('privacy-policy') }}" class="transition-colors hover:text-white">Polityka prywatności</a></li>
                    @endforelse
                    <li><a href="mailto:biuro@cleancodeas.pl" class="transition-colors hover:text-white">Kontakt</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-ink-800/80">
        <div class="container-page flex flex-col items-center justify-between gap-3 py-6 text-xs text-ink-500 sm:flex-row">
            <p>
                &copy; {{ date('Y') }} Projekciarz.pl. Wszelkie prawa zastrzeżone.
                @if(config('services.cookiebot.cbid'))
                    <span class="mx-1.5 text-ink-700">·</span>
                    <button type="button" onclick="window.Cookiebot && Cookiebot.renew()" class="underline-offset-2 transition-colors hover:text-white hover:underline">Ustawienia cookies</button>
                @endif
            </p>
            <p>
                Projekt i realizacja:
                <a href="https://cleancodeas.pl" target="_blank" rel="noopener" class="font-medium text-ink-300 transition-colors hover:text-white">CleanCode</a>
            </p>
        </div>
    </div>
</footer>
