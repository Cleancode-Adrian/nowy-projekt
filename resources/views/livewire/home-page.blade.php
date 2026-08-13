<div>
{{-- Hero Section --}}
<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-6">
                Szukasz <span class="text-yellow-300">wykonawcy</span> strony WWW?<br>
                Opisz projekt, zbierz oferty
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Opublikuj zlecenie, a wykonawcy sami zgłoszą się z wyceną i terminem.
                Porównasz ich portfolio i opinie w jednym miejscu, zanim kogokolwiek wybierzesz.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('announcements.create') }}"
                   data-cta="hero-publikuj"
                   class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition-colors inline-block">
                    Opublikuj zlecenie za darmo
                </a>
                <a href="{{ route('blog.show', 'ile-kosztuje-strona-internetowa-2026') }}"
                   data-cta="hero-cennik"
                   class="bg-blue-700 hover:bg-blue-800 px-8 py-4 rounded-lg font-bold text-lg transition-colors inline-block">
                    Ile to kosztuje?
                </a>
            </div>

            <p class="text-blue-200 text-sm mt-6">
                Publikacja zlecenia jest bezpłatna. Nie musisz od razu wybierać wykonawcy.
            </p>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($stats['announcements']) }}+</div>
                <div class="text-gray-600">Aktywnych ofert</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2">{{ number_format($stats['freelancers']) }}+</div>
                <div class="text-gray-600">Zweryfikowanych wykonawców</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-green-600 mb-2">{{ number_format($stats['categories']) }}</div>
                <div class="text-gray-600">Kategorii usług</div>
            </div>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Popularne kategorie</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Wybierz kategorię i znajdź specjalistę
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('announcements.index', ['category' => $category->slug]) }}"
                   class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 p-6 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl"
                             style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                            <i class="{{ $category->icon }}"></i>
                        </div>
                        <span class="text-sm font-medium px-3 py-1 rounded-full"
                              style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                            {{ $category->announcements_count }} ofert
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                        {{ $category->name }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $category->description }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Announcements --}}
@if($featuredAnnouncements->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">🔥 Pilne projekty</h2>
            <p class="text-xl text-gray-600">
                Te projekty wymagają szybkiej realizacji
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredAnnouncements as $announcement)
                <x-announcement-card :announcement="$announcement" />
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                Zobacz wszystkie ogłoszenia
                <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

{{-- How It Works --}}
<section id="jak-to-dziala" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Jak to działa?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Prosty proces w trzech krokach
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    1
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Dodaj ogłoszenie</h3>
                <p class="text-gray-600">
                    Opisz swój projekt, ustaw budżet i opublikuj zlecenie. To zajmie tylko 2 minuty.
                </p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    2
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Otrzymuj oferty</h3>
                <p class="text-gray-600">
                    Freelancerzy będą składać oferty. Porównaj ceny, terminy i portfolio.
                </p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-green-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    3
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Wybierz najlepszego</h3>
                <p class="text-gray-600">
                    Zaakceptuj ofertę, rozpocznij współpracę i zrealizuj projekt.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Dla zlecających --}}
<section id="dla-zlecajacych" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Nie wiesz, od czego zacząć?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Najczęstsze wątpliwości osób, które pierwszy raz zlecają projekt online.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 mb-2">
                    <i class="fa-solid fa-circle-question text-blue-600 mr-2"></i>
                    Nie znam się na technologiach. Poradzę sobie?
                </h3>
                <p class="text-gray-600">
                    Opisz, co ma robić strona i do kogo jest skierowana - reszta to zadanie wykonawcy.
                    Nie musisz wiedzieć, czy potrzebujesz WordPressa, czy rozwiązania dedykowanego.
                </p>
            </div>

            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 mb-2">
                    <i class="fa-solid fa-wallet text-blue-600 mr-2"></i>
                    Muszę z góry znać budżet?
                </h3>
                <p class="text-gray-600">
                    Wystarczą widełki. Pomagają wykonawcom dopasować zakres do Twoich możliwości.
                    Orientacyjne ceny znajdziesz w naszym
                    <a href="{{ route('blog.show', 'ile-kosztuje-strona-internetowa-2026') }}" class="text-blue-600 hover:underline">przewodniku po wycenach</a>.
                </p>
            </div>

            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 mb-2">
                    <i class="fa-solid fa-shield-halved text-blue-600 mr-2"></i>
                    Skąd mam wiedzieć, komu zaufać?
                </h3>
                <p class="text-gray-600">
                    Przy każdej ofercie widzisz portfolio wykonawcy, jego wcześniejsze realizacje
                    oraz oceny wystawione przez klientów po zakończonych projektach.
                </p>
            </div>

            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="font-bold text-gray-900 mb-2">
                    <i class="fa-solid fa-handshake-slash text-blue-600 mr-2"></i>
                    Co, jeśli żadna oferta mi nie odpowiada?
                </h3>
                <p class="text-gray-600">
                    Publikacja zlecenia do niczego nie zobowiązuje. Możesz nie wybrać nikogo,
                    doprecyzować opis i poczekać na kolejne zgłoszenia.
                </p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('announcements.create') }}"
               data-cta="faq-publikuj"
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg transition-colors">
                Opisz swój projekt
                <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- For Freelancers --}}
<section id="dla-freelancerow" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    Jesteś freelancerem?<br>
                    <span class="text-blue-600">Znajdź zlecenia</span>
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    Przeglądaj zlecenia od klientów, składaj oferty i buduj reputację
                    opartą na ocenach od osób, dla których realnie pracowałeś.
                </p>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fa-solid fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Zlecenia dopasowane do umiejętności</h4>
                            <p class="text-gray-600 text-sm">Powiadomienia o nowych projektach z Twoich kategorii</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fa-solid fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Buduj swoją reputację</h4>
                            <p class="text-gray-600 text-sm">System ocen i portfolio pomogą Ci wyróżnić się</p>
                        </div>
                    </li>
                </ul>

                <a href="{{ route('register') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg transition-colors">
                    Rozpocznij zarabianie
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="relative">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                    <div class="mb-6">
                        <div class="text-4xl font-bold mb-2">{{ number_format($stats['announcements']) }}+</div>
                        <div class="text-blue-100">Aktywnych projektów czeka na Ciebie</div>
                    </div>
                    <div class="space-y-4">
                        @if($stats['avg_budget'])
                            <div class="bg-white/20 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Średni budżet zlecenia</span>
                                    <span class="font-bold">{{ number_format($stats['avg_budget'], 0, ',', ' ') }} PLN</span>
                                </div>
                            </div>
                        @endif
                        <div class="bg-white/20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Kategorii usług</span>
                                <span class="font-bold">{{ $stats['categories'] }}</span>
                            </div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Zweryfikowanych wykonawców</span>
                                <span class="font-bold">{{ number_format($stats['freelancers']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 bg-blue-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Masz projekt do zrealizowania?</h2>
        <p class="text-xl text-blue-100 mb-8">
            Opisanie zlecenia zajmuje kilka minut. Oferty wraz z wycenami przychodzą do Ciebie.
        </p>
        <a href="{{ route('announcements.create') }}"
           data-cta="stopka-publikuj"
           class="inline-flex items-center bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition-colors">
            Opublikuj zlecenie za darmo
        </a>
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

