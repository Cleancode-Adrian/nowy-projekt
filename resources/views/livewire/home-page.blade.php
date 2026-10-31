<div>
@php
    $check = '<svg class="h-5 w-5 shrink-0 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>';
    $arrow = '<svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>';
    $pricingGuide = route('blog.show', 'ile-kosztuje-strona-internetowa-2026');
@endphp

{{-- ============================== HERO ============================== --}}
<section class="relative isolate overflow-hidden bg-ink-950 text-white">
    <div class="bg-grid absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_75%)]"></div>
    <div class="absolute -top-40 left-1/2 -z-10 h-[36rem] w-[64rem] -translate-x-1/2 rounded-full bg-brand-600/25 blur-3xl"></div>

    <div class="container-page grid items-center gap-14 pb-24 pt-16 lg:grid-cols-12 lg:pb-32 lg:pt-24">
        <div class="lg:col-span-6">
            <p class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-brand-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Publikacja zlecenia jest bezpłatna
            </p>

            <h1 class="mt-6 text-4xl font-bold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-[3.5rem]">
                Opisz projekt.<br>
                <span class="text-brand-300">Wykonawcy przyjdą z wyceną.</span>
            </h1>

            <p class="mt-6 max-w-xl text-lg leading-relaxed text-ink-300">
                Szukasz kogoś do strony WWW, sklepu albo aplikacji? Opublikuj zlecenie, a zweryfikowani
                wykonawcy zgłoszą się z ceną i terminem. Porównasz portfolio i opinie w jednym miejscu,
                zanim kogokolwiek wybierzesz.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('announcements.create') }}" data-cta="hero-publikuj" class="btn btn-lg bg-white text-ink-900 shadow-lg hover:bg-brand-50">
                    Opublikuj zlecenie za darmo
                    {!! $arrow !!}
                </a>
                <a href="{{ $pricingGuide }}" data-cta="hero-cennik" class="btn btn-lg text-white ring-1 ring-white/20 ring-inset hover:bg-white/10">
                    Ile to kosztuje?
                </a>
            </div>

            <ul class="mt-10 grid gap-3 text-sm text-ink-300 sm:grid-cols-3">
                <li class="flex items-center gap-2">{!! $check !!} Bez zobowiązań</li>
                <li class="flex items-center gap-2">{!! $check !!} Weryfikowani wykonawcy</li>
                <li class="flex items-center gap-2">{!! $check !!} Opinie po realizacji</li>
            </ul>
        </div>

        {{-- Ilustracja produktu: jak wygląda zlecenie z ofertami --}}
        <div class="relative lg:col-span-6" aria-hidden="true">
            <div class="relative mx-auto max-w-lg rotate-[0.6deg] rounded-2xl bg-white/5 p-2 ring-1 ring-white/10 backdrop-blur">
                <div class="rounded-xl bg-white p-5 text-ink-900 shadow-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium text-ink-400">Twoje zlecenie</p>
                            <p class="mt-1 font-semibold">Sklep internetowy dla lokalnej piekarni</p>
                        </div>
                        <span class="badge badge-success shrink-0">3 nowe oferty</span>
                    </div>
                    <div class="mt-3 flex gap-4 text-xs text-ink-500">
                        <span>Budżet: 6 000 – 9 000 PLN</span>
                        <span>Termin: 6 tygodni</span>
                    </div>

                    <ul class="mt-5 space-y-2.5">
                        @foreach([
                            ['initials' => 'MK', 'role' => 'WooCommerce, UX', 'price' => '7 400 PLN', 'time' => '5 tyg.', 'rating' => '4,9', 'bg' => 'bg-brand-600'],
                            ['initials' => 'AS', 'role' => 'Shopify, SEO', 'price' => '8 200 PLN', 'time' => '4 tyg.', 'rating' => '5,0', 'bg' => 'bg-ink-800'],
                            ['initials' => 'PW', 'role' => 'Laravel, Vue', 'price' => '8 900 PLN', 'time' => '6 tyg.', 'rating' => '4,8', 'bg' => 'bg-emerald-600'],
                        ] as $i => $offer)
                            <li @class(['flex items-center gap-3 rounded-lg border p-3', 'border-brand-200 bg-brand-50/60' => $i === 0, 'border-ink-100' => $i !== 0])>
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold text-white {{ $offer['bg'] }}">{{ $offer['initials'] }}</span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2 w-24 rounded-full bg-ink-200"></span>
                                        <svg class="h-3.5 w-3.5 text-brand-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 0-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-500">{{ $offer['role'] }} · ★ {{ $offer['rating'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold">{{ $offer['price'] }}</p>
                                    <p class="text-xs text-ink-400">{{ $offer['time'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 flex gap-2">
                        <span class="flex-1 rounded-lg bg-brand-600 py-2 text-center text-xs font-semibold text-white">Porównaj oferty</span>
                        <span class="rounded-lg px-3 py-2 text-xs font-semibold text-ink-600 ring-1 ring-ink-200">Napisz</span>
                    </div>
                </div>
            </div>
            <p class="mt-3 text-center text-xs text-ink-500">Poglądowy widok panelu zleceniodawcy</p>
        </div>
    </div>
</section>

{{-- ============================== STATYSTYKI ============================== --}}
<section class="relative z-10 -mt-12">
    <div class="container-page">
        <dl class="grid grid-cols-1 divide-y divide-ink-100 rounded-2xl border border-ink-100 bg-white shadow-lg sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <div class="px-6 py-6 text-center sm:py-8">
                <dt class="text-sm text-ink-500">Aktywnych zleceń</dt>
                <dd class="mt-1 text-3xl font-bold tracking-tight text-ink-900">{{ number_format($stats['announcements'], 0, ',', ' ') }}</dd>
            </div>
            <div class="px-6 py-6 text-center sm:py-8">
                <dt class="text-sm text-ink-500">Zweryfikowanych wykonawców</dt>
                <dd class="mt-1 text-3xl font-bold tracking-tight text-ink-900">{{ number_format($stats['freelancers'], 0, ',', ' ') }}</dd>
            </div>
            <div class="px-6 py-6 text-center sm:py-8">
                <dt class="text-sm text-ink-500">Kategorii usług</dt>
                <dd class="mt-1 text-3xl font-bold tracking-tight text-ink-900">{{ $stats['categories'] }}</dd>
            </div>
        </dl>
    </div>
</section>

{{-- ============================== KATEGORIE ============================== --}}
<section class="py-20 lg:py-24">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div class="max-w-2xl">
                <p class="eyebrow">Kategorie</p>
                <h2 class="section-title mt-2">W czym mogą Ci pomóc wykonawcy</h2>
            </div>
            <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700">
                Wszystkie zlecenia {!! $arrow !!}
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categories as $category)
                <a href="{{ route('announcements.index', ['category' => $category->slug]) }}"
                   class="group flex items-start gap-4 rounded-xl border border-ink-100 bg-white p-5 transition-all hover:border-brand-200 hover:shadow-md">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-lg"
                          style="background-color: {{ $category->color }}14; color: {{ $category->color }};">
                        <i class="{{ $category->icon }}"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-ink-900 group-hover:text-brand-700">{{ $category->name }}</span>
                            <span class="shrink-0 text-xs text-ink-400">{{ $category->announcements_count }} {{ $category->announcements_count == 1 ? 'zlecenie' : 'zleceń' }}</span>
                        </span>
                        @if($category->description)
                            <span class="mt-1 line-clamp-2 block text-sm text-ink-500">{{ $category->description }}</span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== PILNE ZLECENIA ============================== --}}
@if($featuredAnnouncements->count() > 0)
<section class="border-y border-ink-100 bg-white py-20 lg:py-24">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div class="max-w-2xl">
                <p class="eyebrow text-red-600"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-red-500"></span> Pilne</p>
                <h2 class="section-title mt-2">Projekty z krótkim terminem</h2>
                <p class="section-lead">Zleceniodawcy szukają wykonawców, którzy mogą zacząć od razu.</p>
            </div>
            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Przeglądaj wszystkie</a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($featuredAnnouncements as $announcement)
                <x-announcement-card :announcement="$announcement" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================== JAK TO DZIAŁA ============================== --}}
<section id="jak-to-dziala" class="scroll-mt-20 py-20 lg:py-24">
    <div class="container-page">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow">Jak to działa</p>
            <h2 class="section-title mt-2">Od pomysłu do wykonawcy w trzech krokach</h2>
        </div>

        <ol class="relative mt-14 grid gap-10 md:grid-cols-3 md:gap-8">
            <div class="absolute left-[16.66%] right-[16.66%] top-6 hidden h-px bg-gradient-to-r from-brand-200 via-brand-300 to-brand-200 md:block" aria-hidden="true"></div>
            @foreach([
                ['n' => 1, 'title' => 'Opisz zlecenie', 'text' => 'Napisz, co ma powstać, ustaw widełki budżetu i termin. Zajmuje to kilka minut - bez technicznego żargonu.'],
                ['n' => 2, 'title' => 'Zbierz oferty', 'text' => 'Wykonawcy odpowiadają z wyceną, terminem i portfolio. Wszystko masz obok siebie, nie w skrzynce mailowej.'],
                ['n' => 3, 'title' => 'Wybierz i działaj', 'text' => 'Zaakceptuj najlepszą ofertę, prowadź rozmowę na platformie, a po zakończeniu wystaw ocenę.'],
            ] as $step)
                <li class="relative text-center">
                    <span class="relative mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-lg font-bold text-brand-600 shadow-sm ring-1 ring-brand-200">{{ $step['n'] }}</span>
                    <h3 class="mt-5 text-lg font-semibold">{{ $step['title'] }}</h3>
                    <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-ink-500">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============================== ZAKRES USŁUGI ============================== --}}
<section id="zakres-uslugi" class="scroll-mt-20 border-y border-ink-100 bg-white py-20 lg:py-24">
    <div class="container-page">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow">Zakres usługi</p>
            <h2 class="section-title mt-2">Za co odpowiadamy, a co ustalasz sam</h2>
            <p class="section-lead">Mówimy o tym wprost, żebyś nie odkrył tego dopiero w trakcie projektu.</p>
        </div>

        <div class="mx-auto mt-12 grid max-w-5xl gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-ink-100 bg-gradient-to-b from-emerald-50/70 to-white p-8">
                <h3 class="flex items-center gap-3 text-lg font-semibold">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700"><i class="fa-solid fa-shield-halved"></i></span>
                    To bierzemy na siebie
                </h3>
                <ul class="mt-6 space-y-4 text-sm">
                    @foreach([
                        ['Dotarcie do wykonawców.', 'Twoje zlecenie trafia do specjalistów z wybranej kategorii.'],
                        ['Weryfikację kont wykonawców.', 'Każde konto wykonawcy zatwierdza administrator, zanim zacznie składać oferty.'],
                        ['Oferty w jednym miejscu.', 'Wycena, termin i portfolio obok siebie - bez przekopywania skrzynki mailowej.'],
                        ['Moderację ogłoszeń i opinii.', 'Zlecenia i oceny przechodzą sprawdzenie przed publikacją.'],
                        ['Historię współpracy.', 'Po zakończonym projekcie wystawiasz ocenę, którą widzą kolejni zleceniodawcy.'],
                    ] as [$lead, $text])
                        <li class="flex gap-3">
                            {!! $check !!}
                            <p><span class="font-semibold text-ink-900">{{ $lead }}</span> <span class="text-ink-600">{{ $text }}</span></p>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-2xl border border-ink-100 bg-ink-50/60 p-8">
                <h3 class="flex items-center gap-3 text-lg font-semibold">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-ink-200/70 text-ink-700"><i class="fa-solid fa-handshake"></i></span>
                    To ustalasz bezpośrednio z wykonawcą
                </h3>
                <ul class="mt-6 space-y-4 text-sm">
                    @foreach([
                        ['Płatność.', 'Nie pośredniczymy w przelewach i nie prowadzimy rachunku powierniczego. Pieniądze idą bezpośrednio do wykonawcy.'],
                        ['Fakturę lub rachunek.', 'Dokument księgowy wystawia wykonawca, na zasadach swojej formy działalności.'],
                        ['Umowę i zakres prac.', 'Warto spisać zakres, etapy odbioru i prawa do kodu, zanim ruszy praca.'],
                        ['Harmonogram.', 'Terminy etapów uzgadniacie między sobą - my ich nie egzekwujemy.'],
                    ] as [$lead, $text])
                        <li class="flex gap-3">
                            <svg class="h-5 w-5 shrink-0 text-ink-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM6.75 9.25a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd"/></svg>
                            <p><span class="font-semibold text-ink-900">{{ $lead }}</span> <span class="text-ink-600">{{ $text }}</span></p>
                        </li>
                    @endforeach
                </ul>

                <p class="mt-6 border-t border-ink-200 pt-5 text-sm text-ink-600">
                    Dlatego przy każdej ofercie widzisz portfolio i oceny wykonawcy - to na ich podstawie
                    decydujesz, komu powierzyć projekt. Zobacz też
                    <a href="{{ $pricingGuide }}" class="font-medium text-brand-600 underline-offset-2 hover:underline">na co zwrócić uwagę w wycenie</a>.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ============================== FAQ DLA ZLECAJĄCYCH ============================== --}}
<section id="dla-zlecajacych" class="scroll-mt-20 py-20 lg:py-24">
    <div class="container-page grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <p class="eyebrow">Pierwsze zlecenie</p>
            <h2 class="section-title mt-2">Nie wiesz, od czego zacząć?</h2>
            <p class="section-lead">Najczęstsze wątpliwości osób, które pierwszy raz zlecają projekt online.</p>
            <a href="{{ route('announcements.create') }}" data-cta="faq-publikuj" class="btn btn-primary btn-lg mt-8">
                Opisz swój projekt {!! $arrow !!}
            </a>
        </div>

        <div class="divide-y divide-ink-100 rounded-2xl border border-ink-100 bg-white lg:col-span-8" x-data="{ open: 0 }">
            @foreach([
                ['Nie znam się na technologiach. Poradzę sobie?', 'Opisz, co ma robić strona i do kogo jest skierowana - reszta to zadanie wykonawcy. Nie musisz wiedzieć, czy potrzebujesz WordPressa, czy rozwiązania dedykowanego.', null],
                ['Muszę z góry znać budżet?', 'Wystarczą widełki. Pomagają wykonawcom dopasować zakres do Twoich możliwości. Orientacyjne ceny znajdziesz w naszym', 'przewodniku po wycenach'],
                ['Skąd mam wiedzieć, komu zaufać?', 'Przy każdej ofercie widzisz portfolio wykonawcy, jego wcześniejsze realizacje oraz oceny wystawione przez klientów po zakończonych projektach.', null],
                ['Co, jeśli żadna oferta mi nie odpowiada?', 'Publikacja zlecenia do niczego nie zobowiązuje. Możesz nie wybrać nikogo, doprecyzować opis i poczekać na kolejne zgłoszenia.', null],
            ] as $i => [$q, $a, $link])
                <div>
                    <h3>
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}"
                                class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-ink-900 hover:text-brand-700">
                            {{ $q }}
                            <svg class="h-5 w-5 shrink-0 text-ink-400 transition-transform" :class="open === {{ $i }} && 'rotate-45'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                        </button>
                    </h3>
                    <div x-show="open === {{ $i }}" x-collapse @if($i !== 0) x-cloak @endif class="px-6 pb-5 text-sm leading-relaxed text-ink-600">
                        {{ $a }}
                        @if($link)
                            <a href="{{ $pricingGuide }}" class="font-medium text-brand-600 hover:underline">{{ $link }}</a>.
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== DLA WYKONAWCÓW ============================== --}}
<section id="dla-freelancerow" class="scroll-mt-20 pb-20 lg:pb-24">
    <div class="container-page">
        <div class="relative isolate overflow-hidden rounded-3xl bg-ink-900 px-6 py-14 sm:px-12 lg:px-16">
            <div class="absolute -right-24 -top-24 -z-10 h-80 w-80 rounded-full bg-brand-600/30 blur-3xl" aria-hidden="true"></div>

            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <p class="eyebrow text-brand-300">Dla wykonawców</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Szukasz zleceń? Zbuduj reputację na realnych projektach</h2>
                    <p class="mt-4 text-lg leading-relaxed text-ink-300">
                        Przeglądaj zlecenia od klientów, składaj oferty i zbieraj oceny od osób,
                        dla których faktycznie pracowałeś.
                    </p>
                    <ul class="mt-8 space-y-4">
                        <li class="flex gap-3">
                            {!! $check !!}
                            <div>
                                <p class="font-semibold text-white">Zlecenia dopasowane do umiejętności</p>
                                <p class="text-sm text-ink-400">Powiadomienia o nowych projektach z Twoich kategorii.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            {!! $check !!}
                            <div>
                                <p class="font-semibold text-white">Portfolio i oceny w jednym profilu</p>
                                <p class="text-sm text-ink-400">Zleceniodawca widzi Twoje realizacje obok wyceny.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-lg mt-10 bg-white text-ink-900 hover:bg-brand-50">
                        Załóż konto wykonawcy {!! $arrow !!}
                    </a>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 sm:col-span-2">
                        <dt class="text-sm text-ink-400">Aktywnych projektów czeka na oferty</dt>
                        <dd class="mt-2 text-4xl font-bold tracking-tight text-white">{{ number_format($stats['announcements'], 0, ',', ' ') }}</dd>
                    </div>
                    @if(!empty($stats['avg_budget']))
                        <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                            <dt class="text-sm text-ink-400">Średni budżet zlecenia</dt>
                            <dd class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['avg_budget'], 0, ',', ' ') }} PLN</dd>
                        </div>
                    @endif
                    <div @class(['rounded-2xl bg-white/5 p-6 ring-1 ring-white/10', 'sm:col-span-2' => empty($stats['avg_budget'])])>
                        <dt class="text-sm text-ink-400">Kategorii usług</dt>
                        <dd class="mt-2 text-2xl font-bold text-white">{{ $stats['categories'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>

{{-- ============================== CTA ============================== --}}
<section class="border-t border-ink-100 bg-white">
    <div class="container-page flex flex-col items-center gap-6 py-16 text-center lg:flex-row lg:justify-between lg:text-left">
        <div>
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Masz projekt do zrealizowania?</h2>
            <p class="mt-2 text-ink-500">Opisanie zlecenia zajmuje kilka minut. Oferty z wycenami przychodzą do Ciebie.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('announcements.create') }}" data-cta="stopka-publikuj" class="btn btn-primary btn-lg">Opublikuj zlecenie za darmo</a>
            <a href="#jak-to-dziala" class="btn btn-secondary btn-lg">Jak to działa</a>
        </div>
    </div>
</section>

{{-- Schema.org JSON-LD for SEO --}}
@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "Projekciarz.pl",
    "description": "Platforma łącząca klientów z freelancerami",
    "url": "{{ url('/') }}",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ route('announcements.index') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endpush

{{-- Śledzenie konwersji: każde kliknięcie w CTA trafia do dataLayer,
     żeby dało się z tego zbudować konwersję w Google Ads / GA4. --}}
@push('scripts')
<script>
    document.addEventListener('click', function (event) {
        var cta = event.target.closest('[data-cta]');
        if (!cta) return;

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'cta_klik',
            cta_id: cta.getAttribute('data-cta'),
            cta_tekst: cta.textContent.trim()
        });
    });
</script>
@endpush
</div>
