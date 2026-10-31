<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Cztery poradniki dla zleceniodawców (wybór wykonawcy, umowa, odbiór, terminy)
 * oraz okładki dla wpisów, które ich nie miały. Seeder jest idempotentny -
 * można go uruchamiać wielokrotnie: php artisan db:seed --class=BlogPostClientGuidesSeeder
 */
class BlogPostClientGuidesSeeder extends Seeder
{
    /** Okładki (storage/app/public/blog) dla istniejących wpisów bez obrazka. */
    private const COVERS = [
        'ile-kosztuje-strona-internetowa-2026' => [
            'blog/ile-kosztuje-strona-internetowa-2026.png',
            'Ile kosztuje strona internetowa w 2026 - widełki cenowe i czynniki wyceny',
        ],
        'jak-opisac-zlecenie-zeby-dostac-dobre-oferty' => [
            'blog/jak-opisac-zlecenie-zeby-dostac-dobre-oferty.png',
            'Jak opisać zlecenie, żeby dostać dobre oferty - brief w 5 krokach',
        ],
    ];

    public function run(): void
    {
        $author = User::where('role', 'admin')->first();

        if (!$author) {
            $this->command?->warn('Brak konta admina - pomijam BlogPostClientGuidesSeeder.');
            return;
        }

        foreach (self::COVERS as $slug => [$image, $alt]) {
            BlogPost::where('slug', $slug)
                ->where(fn ($q) => $q->whereNull('featured_image')->orWhere('featured_image', ''))
                ->update(['featured_image' => $image, 'featured_image_alt' => $alt]);
        }

        // Wpisy publikujemy w odstępach, żeby lista bloga nie pokazała czterech
        // tekstów z tą samą datą - i żeby nie wyglądało to na zrzut z generatora.
        $publishedAt = now()->subDays(9);

        foreach ($this->posts() as $data) {
            $tagIds = [];
            foreach ($data['tags'] as $name) {
                $tag = Tag::updateOrCreate(
                    ['name' => $name, 'type' => 'blog'],
                    ['slug' => Str::slug($name), 'type' => 'blog'],
                );
                $tagIds[] = $tag->id;
            }

            $existing = BlogPost::where('slug', $data['slug'])->first();

            $post = BlogPost::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'author_id' => $author->id,
                    'category_id' => Category::where('slug', $data['category'])->value('id'),
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'meta_keywords' => $data['meta_keywords'],
                    'featured_image' => 'blog/' . $data['slug'] . '.png',
                    'featured_image_alt' => $data['image_alt'],
                    'content' => $data['content'],
                    'status' => 'published',
                    'published_at' => $existing?->published_at ?? $publishedAt,
                ],
            );

            $post->tags()->syncWithoutDetaching($tagIds);
            $this->command?->info('Wpis opublikowany: /blog/' . $post->slug);

            $publishedAt = $publishedAt->copy()->addDays(3);
        }
    }

    private function posts(): array
    {
        return [
            [
                'slug' => 'jak-wybrac-wykonawce-strony-internetowej',
                'category' => 'strona-firmowa',
                'title' => 'Jak wybrać wykonawcę strony internetowej? 9 pytań, które warto zadać przed podpisaniem umowy',
                'excerpt' => 'Portfolio, opinie i cena to dopiero początek. Lista pytań, które pokazują, czy wykonawca rozumie Twój biznes, jak pracuje i co się stanie, kiedy coś pójdzie nie tak - oraz odpowiedzi, które powinny zapalić czerwoną lampkę.',
                'meta_title' => 'Jak wybrać wykonawcę strony internetowej? 9 pytań przed umową',
                'meta_description' => 'Jak sprawdzić freelancera lub agencję przed zleceniem strony WWW: pytania o portfolio, proces, prawa do kodu, hosting i wsparcie po wdrożeniu. Czerwone flagi w ofertach.',
                'meta_keywords' => ['jak wybrać wykonawcę strony', 'freelancer czy agencja', 'wykonawca strony internetowej', 'jak sprawdzić freelancera', 'zlecenie strony www'],
                'image_alt' => 'Jak wybrać wykonawcę strony internetowej - 9 pytań przed podpisaniem umowy',
                'tags' => ['Dla klientów', 'Poradnik', 'Wycena'],
                'content' => $this->contentChoosing(),
            ],
            [
                'slug' => 'umowa-z-freelancerem-na-strone-www',
                'category' => 'strona-firmowa',
                'title' => 'Umowa z freelancerem na stronę WWW: co musi się w niej znaleźć, żeby obie strony spały spokojnie',
                'excerpt' => 'Zakres, etapy, poprawki, prawa autorskie, dostępy i co się dzieje, gdy projekt stanie w miejscu. Punkt po punkcie, bez prawniczego żargonu - plus zapisy, które najczęściej kończą się sporem, bo ich zabrakło.',
                'meta_title' => 'Umowa z freelancerem na stronę WWW - co musi zawierać',
                'meta_description' => 'Co zapisać w umowie na wykonanie strony internetowej: zakres i podstrony, etapy i płatności, liczba poprawek, przeniesienie praw autorskich, dostępy, terminy i kary umowne.',
                'meta_keywords' => ['umowa na stronę internetową', 'umowa z freelancerem', 'umowa o dzieło strona www', 'prawa autorskie do strony', 'przeniesienie praw do kodu'],
                'image_alt' => 'Umowa z freelancerem na stronę WWW - co musi się w niej znaleźć',
                'tags' => ['Dla klientów', 'Poradnik'],
                'content' => $this->contentContract(),
            ],
            [
                'slug' => 'odbior-strony-internetowej-checklista',
                'category' => 'strona-firmowa',
                'title' => 'Odbiór strony internetowej: checklista 24 punktów do sprawdzenia przed ostatnią płatnością',
                'excerpt' => 'Strona „wygląda dobrze" to za mało. Co przeklikać na telefonie, co sprawdzić w formularzach, dostępach, SEO i kopiach zapasowych - zanim przelejesz ostatnią transzę i wykonawca przejdzie do kolejnego projektu.',
                'meta_title' => 'Odbiór strony internetowej - checklista 24 punktów',
                'meta_description' => 'Lista kontrolna odbioru strony WWW: responsywność, formularze, szybkość, SEO, dostępy do domeny i hostingu, kopie zapasowe, RODO. Co sprawdzić przed końcową płatnością.',
                'meta_keywords' => ['odbiór strony internetowej', 'checklista strony www', 'protokół odbioru strony', 'co sprawdzić przed odbiorem strony', 'testy strony internetowej'],
                'image_alt' => 'Odbiór strony internetowej - checklista przed ostatnią płatnością',
                'tags' => ['Dla klientów', 'Poradnik', 'SEO'],
                'content' => $this->contentAcceptance(),
            ],
            [
                'slug' => 'ile-trwa-zrobienie-strony-internetowej',
                'category' => 'strona-firmowa',
                'title' => 'Ile trwa zrobienie strony lub sklepu internetowego? Realne terminy etapów i co je najczęściej wydłuża',
                'excerpt' => 'Wizytówka w tydzień, sklep w kwartał - skąd biorą się te liczby i dlaczego połowa projektów kończy się później, niż zakładano. Harmonogram etap po etapie i cztery rzeczy po stronie klienta, które skracają go najbardziej.',
                'meta_title' => 'Ile trwa zrobienie strony internetowej? Realne terminy',
                'meta_description' => 'Ile czasu zajmuje wykonanie strony wizytówki, strony firmowej, sklepu internetowego i aplikacji. Harmonogram etapów, typowe opóźnienia i co zrobić, żeby projekt skończył się w terminie.',
                'meta_keywords' => ['ile trwa zrobienie strony internetowej', 'czas realizacji strony www', 'ile trwa stworzenie sklepu internetowego', 'harmonogram projektu strony', 'etapy tworzenia strony'],
                'image_alt' => 'Ile trwa zrobienie strony lub sklepu internetowego - realne terminy etapów',
                'tags' => ['Dla klientów', 'Poradnik', 'E-commerce'],
                'content' => $this->contentTimeline(),
            ],
        ];
    }

    private function contentChoosing(): string
    {
        return <<<'HTML'
<p>Większość osób zlecających pierwszą stronę porównuje trzy rzeczy: portfolio, opinie i cenę. To rozsądny początek, ale wszystkie trzy da się „podrasować", a żadna nie mówi, jak będzie wyglądała współpraca, kiedy pojawi się pierwszy problem. Poniżej dziewięć pytań, które zadaj wykonawcy zanim podpiszesz cokolwiek - i odpowiedzi, które powinny Cię zaniepokoić.</p>

<h2>Zanim zaczniesz: freelancer czy agencja?</h2>

<p>To nie jest pytanie „kto lepszy", tylko „kto pasuje do skali projektu".</p>

<ul>
<li><strong>Freelancer</strong> - niższa cena, bezpośredni kontakt z osobą, która faktycznie robi stronę, szybsze decyzje. Ryzyko: jedna osoba oznacza jedną chorobę, jeden urlop, jedną kolejkę zleceń. Dobry wybór dla wizytówek, stron firmowych i mniejszych sklepów.</li>
<li><strong>Agencja</strong> - zespół (projektant, programista, osoba od treści), procesy, zastępowalność. Płacisz za to więcej, a kontakt często idzie przez opiekuna klienta, nie przez wykonawcę. Sensowna przy większych sklepach, aplikacjach i projektach z wieloma integracjami.</li>
</ul>

<p>W praktyce wielu doświadczonych freelancerów pracuje w luźnych zespołach i bierze większe projekty - warto o to zapytać, zamiast zakładać.</p>

<h2>9 pytań do wykonawcy</h2>

<h3>1. „Pokaż mi dwie realizacje najbardziej podobne do mojego projektu - i powiedz, co w nich zrobiłbyś dziś inaczej"</h3>

<p>Portfolio pokazuje najlepsze prace. Prośba o <em>podobne</em> projekty pokazuje, czy wykonawca robił już coś w Twojej skali i branży. Druga część pytania mówi jeszcze więcej: ktoś, kto potrafi skrytykować własną pracę, zwykle potrafi też przyjąć uwagi od Ciebie.</p>

<p><strong>Czerwona flaga:</strong> portfolio bez linków do działających stron albo strony, które po wejściu wyglądają zupełnie inaczej niż na zrzutach.</p>

<h3>2. „Jak wygląda Twój proces od briefu do wdrożenia?"</h3>

<p>Oczekujesz konkretnej odpowiedzi: najpierw ustalenie zakresu, potem makiety albo projekt graficzny, akceptacja, kodowanie, testy, wdrożenie. Z informacją, w których momentach <em>Ty</em> musisz coś dostarczyć lub zatwierdzić.</p>

<p><strong>Czerwona flaga:</strong> „zaczynamy od razu, zobaczysz jak będzie gotowe". Brak etapów oznacza, że pierwszy raz zobaczysz stronę, gdy zmiany będą już drogie.</p>

<h3>3. „Co dokładnie zawiera cena, a co jest poza nią?"</h3>

<p>Poproś o listę: liczba podstron, projekt graficzny, teksty, zdjęcia, formularze, integracje (płatności, newsletter, mapy), wersje językowe, szkolenie z obsługi. Wszystko, czego nie ma na liście, jest poza wyceną - i tak to potraktuj.</p>

<p><strong>Czerwona flaga:</strong> jedna kwota „za stronę" bez rozbicia na elementy. Więcej o tym, co realnie wpływa na cenę, znajdziesz w tekście <a href="/blog/ile-kosztuje-strona-internetowa-2026">Ile kosztuje strona internetowa w 2026</a>.</p>

<h3>4. „Ile rund poprawek jest w cenie i co jest poprawką, a co zmianą zakresu?"</h3>

<p>To najczęstsze źródło konfliktów. Przesunięcie przycisku to poprawka. Dołożenie podstrony „O nas", której nie było w briefie, to zmiana zakresu. Dobry wykonawca sam rozróżnia te dwie rzeczy i mówi o tym od razu.</p>

<h3>5. „Czy po zapłacie kod, pliki źródłowe i projekt graficzny są moje?"</h3>

<p>Odpowiedź powinna brzmieć „tak" i powinna trafić do umowy jako przeniesienie praw autorskich majątkowych. Bez tego formalnie możesz nie mieć prawa modyfikować własnej strony u innego wykonawcy.</p>

<p><strong>Czerwona flaga:</strong> „strona działa na naszym systemie, więc pliki zostają u nas". To model, w którym zmiana wykonawcy oznacza budowę strony od zera.</p>

<h3>6. „Na kogo będą zarejestrowane domena i hosting?"</h3>

<p>Na Ciebie. Zawsze. Wykonawca może wszystko skonfigurować, ale konto u rejestratora domeny i u dostawcy hostingu ma być Twoje, z Twoim e-mailem i Twoją kartą. Zbyt wiele firm straciło domenę, bo była zarejestrowana na agencję, która przestała istnieć albo z którą się pokłóciły.</p>

<h3>7. „Co się dzieje po wdrożeniu? Kto robi aktualizacje i ile to kosztuje?"</h3>

<p>Strona na WordPressie bez aktualizacji przez rok to realne ryzyko włamania. Zapytaj, czy wykonawca oferuje opiekę (i za ile miesięcznie), czy przekazuje Ci stronę z instrukcją i znika. Oba modele są w porządku - ważne, żebyś wiedział, na który się piszesz.</p>

<h3>8. „W jakiej technologii zrobisz stronę i dlaczego akurat w tej?"</h3>

<p>Nie musisz znać technologii. Chcesz usłyszeć uzasadnienie w kategoriach <em>Twojego</em> projektu: „WordPress, bo sam będziesz dodawał wpisy", „Shopify, bo nie chcesz zajmować się utrzymaniem sklepu", „kod dedykowany, bo masz nietypową integrację". Jeśli uzasadnienie brzmi „bo w tym robię" - to też informacja.</p>

<h3>9. „Co zrobisz, jeśli nie dotrzymasz terminu?"</h3>

<p>Nie chodzi o kary. Chodzi o to, czy wykonawca ma w ogóle plan: informuje z wyprzedzeniem, proponuje przesunięcie etapu, czy po prostu milknie. Odpowiedź „u mnie nie ma opóźnień" jest gorsza niż „zdarza się, wtedy robię X".</p>

<h2>Jak porównać oferty, które wyglądają zupełnie inaczej</h2>

<p>Gdy dostaniesz kilka wycen, sprowadź je do wspólnego mianownika. Prosta tabela wystarczy:</p>

<table>
<thead>
<tr><th>Kryterium</th><th>Oferta A</th><th>Oferta B</th><th>Oferta C</th></tr>
</thead>
<tbody>
<tr><td>Cena całkowita</td><td></td><td></td><td></td></tr>
<tr><td>Liczba podstron / unikalnych widoków</td><td></td><td></td><td></td></tr>
<tr><td>Projekt graficzny w cenie?</td><td></td><td></td><td></td></tr>
<tr><td>Teksty i zdjęcia w cenie?</td><td></td><td></td><td></td></tr>
<tr><td>Rundy poprawek</td><td></td><td></td><td></td></tr>
<tr><td>Prawa do kodu i projektu</td><td></td><td></td><td></td></tr>
<tr><td>Termin realizacji</td><td></td><td></td><td></td></tr>
<tr><td>Opieka po wdrożeniu (koszt/mies.)</td><td></td><td></td><td></td></tr>
</tbody>
</table>

<p>Najtańsza oferta po takim rozpisaniu rzadko pozostaje najtańsza. Najdroższa czasem okazuje się jedyną kompletną.</p>

<h2>Podsumowanie</h2>

<p>Dobry wykonawca nie boi się żadnego z tych pytań - większość z nich sam poruszy, zanim zdążysz je zadać. Jeśli na kilka z nich dostajesz wymijające odpowiedzi, nie negocjuj ceny. Szukaj dalej.</p>

<p>Najszybszy sposób, żeby porównać kilku wykonawców na raz, to <a href="/announcements/create">opublikowanie zlecenia na Projekciarz.pl</a>. Oferty przychodzą razem z portfolio i ocenami od poprzednich klientów, a pytania z tej listy możesz zadać każdemu w wiadomości, zanim kogokolwiek wybierzesz.</p>
HTML;
    }

    private function contentContract(): string
    {
        return <<<'HTML'
<p>„Dogadaliśmy się mailowo" to najczęstsze zdanie, które pada na początku sporu o stronę internetową. Umowa nie musi być długa ani pisana przez prawnika - musi za to odpowiadać na kilka pytań, które w trakcie projektu pojawią się na pewno. Poniżej lista tych pytań i to, jak je zapisać.</p>

<p><em>Ten tekst to praktyczny przewodnik, nie porada prawna. Przy większych projektach warto skonsultować wzór z prawnikiem.</em></p>

<h2>Umowa o dzieło czy umowa o świadczenie usług?</h2>

<p>Strona internetowa ma konkretny, odbieralny rezultat, więc najczęściej podpisuje się <strong>umowę o dzieło</strong>. Jeśli zlecasz też opiekę po wdrożeniu (aktualizacje, drobne zmiany, hosting), ta część powinna być osobną umową o świadczenie usług albo osobnym rozdziałem z własnym okresem wypowiedzenia. Mieszanie obu w jednym zdaniu kończy się tym, że nikt nie wie, kiedy „dzieło" zostało odebrane.</p>

<h2>1. Zakres - czyli co dokładnie powstanie</h2>

<p>To najważniejsza część i jednocześnie ta, którą najczęściej załatwia się jednym zdaniem „wykonanie strony internetowej". Zamiast tego zapisz:</p>

<ul>
<li><strong>Listę podstron</strong> z zaznaczeniem, które mają unikalny układ (strona główna, oferta, kontakt), a które powstają z szablonu (np. wpisy bloga, podstrony usług).</li>
<li><strong>Funkcje:</strong> formularz kontaktowy, wyszukiwarka, koszyk, płatności, newsletter, wersje językowe, panel do samodzielnej edycji treści.</li>
<li><strong>Integracje:</strong> z jakimi systemami zewnętrznymi (płatności, kurierzy, CRM, fakturowanie) i kto dostarcza do nich dostępy.</li>
<li><strong>Co dostarcza klient:</strong> teksty, zdjęcia, logo, dostępy do domeny. Z terminami - bo opóźnienie po Twojej stronie przesuwa termin po stronie wykonawcy.</li>
<li><strong>Czego umowa nie obejmuje:</strong> jedno zdanie, które oszczędza tygodni dyskusji. Np. „umowa nie obejmuje pisania tekstów, sesji zdjęciowej, konfiguracji poczty e-mail i pozycjonowania".</li>
</ul>

<p>Dobrze przygotowany brief można podpiąć do umowy jako załącznik - wtedy nie trzeba przepisywać go do paragrafów. Jak taki brief napisać, opisaliśmy w tekście <a href="/blog/jak-opisac-zlecenie-zeby-dostac-dobre-oferty">Jak opisać zlecenie, żeby dostać dobre oferty</a>.</p>

<h2>2. Etapy, terminy i akceptacje</h2>

<p>Projekt podziel na etapy z osobnym terminem i osobną akceptacją. Typowy podział:</p>

<ol>
<li>Makiety / projekt graficzny strony głównej - akceptacja klienta.</li>
<li>Projekt pozostałych podstron - akceptacja.</li>
<li>Wersja robocza strony (kodowanie) na adresie testowym - testy i lista uwag.</li>
<li>Wdrożenie na docelową domenę - odbiór końcowy.</li>
</ol>

<p>Przy każdym etapie zapisz, <strong>ile dni ma klient na zgłoszenie uwag</strong> (zwykle 3-7 dni roboczych) i co się dzieje, gdy ich nie zgłosi - najuczciwiej: etap uznaje się za przyjęty. Chroni to obie strony: wykonawca nie czeka w nieskończoność, a Ty wiesz, kiedy masz usiąść do przeglądu.</p>

<h2>3. Poprawki: ile, jakie i co nie jest poprawką</h2>

<p>Zapisz liczbę rund poprawek na każdym etapie (standard to 2-3) i definicję poprawki. Przykład, który działa:</p>

<blockquote><p>Poprawką jest zmiana w obrębie zaakceptowanego zakresu: układ, kolory, teksty, rozmieszczenie elementów. Dodanie nowej podstrony, funkcji lub integracji jest zmianą zakresu i podlega osobnej wycenie.</p></blockquote>

<p>Bez tej definicji każda rozmowa o „jeszcze jednej małej rzeczy" będzie negocjacją.</p>

<h2>4. Wynagrodzenie i płatności</h2>

<ul>
<li><strong>Kwota</strong> - netto czy brutto i czy wykonawca wystawia fakturę VAT, czy rachunek. To zmienia realny koszt o 23%.</li>
<li><strong>Transze</strong> powiązane z etapami. Typowo: 30% zaliczki na start, 40% po akceptacji projektu graficznego, 30% po odbiorze. Zaliczka jest normalna - wykonawca rezerwuje dla Ciebie czas.</li>
<li><strong>Termin płatności</strong> od wystawienia faktury (7-14 dni) i co się dzieje przy opóźnieniu po stronie klienta - np. wstrzymanie prac.</li>
<li><strong>Zmiany zakresu</strong> - na jakiej podstawie wyceniane (stawka godzinowa albo wycena każdorazowa) i że wymagają potwierdzenia na piśmie, choćby mailem.</li>
</ul>

<h2>5. Prawa autorskie - punkt, który najczęściej pomija się w ogóle</h2>

<p>Projekt graficzny i kod strony są utworami. Jeśli umowa nie mówi nic o prawach, formalnie zostają one przy wykonawcy, a Ty masz co najwyżej domniemaną licencję na korzystanie. Zapisz:</p>

<ul>
<li><strong>Przeniesienie autorskich praw majątkowych</strong> do projektu graficznego i kodu z chwilą zapłaty całości wynagrodzenia, na wymienionych polach eksploatacji (co najmniej: utrwalanie, zwielokrotnianie, publiczne udostępnianie w sieci, modyfikowanie).</li>
<li><strong>Prawo do modyfikacji</strong> i zlecania modyfikacji innym osobom - bez tego zmiana wykonawcy jest prawnie wątpliwa.</li>
<li><strong>Wyjątki:</strong> motywy, wtyczki, biblioteki i zdjęcia stockowe mają własne licencje, których wykonawca nie może Ci „przenieść". Umowa powinna je wymienić, a Ty powinieneś dostać dowody zakupu licencji na siebie.</li>
<li><strong>Wykorzystanie w portfolio</strong> - większość wykonawców chce pokazać projekt. To normalne; zapisz, czy się zgadzasz i od kiedy.</li>
</ul>

<h2>6. Dostępy i własność kont</h2>

<p>Domena, hosting, konto w systemie płatności, Google Analytics - wszystko zakładane na Twoje dane, Twój e-mail i Twoją kartę. Wykonawca dostaje dostęp jako współpracownik, a po zakończeniu projektu przekazuje komplet haseł i dostępów administracyjnych. Jedno zdanie w umowie wystarczy, a oszczędza sytuacji, w której firma nie może wejść na własną stronę.</p>

<h2>7. Co, gdy coś pójdzie nie tak</h2>

<ul>
<li><strong>Opóźnienie wykonawcy:</strong> kara umowna za dzień zwłoki (np. 0,2-0,5% wynagrodzenia), ale z zastrzeżeniem, że opóźnienia wynikające z braku materiałów od klienta przesuwają termin.</li>
<li><strong>Odstąpienie od umowy:</strong> kiedy każda ze stron może odstąpić i co się dzieje z pracą wykonaną do tej pory (zwykle: klient płaci za zaakceptowane etapy i dostaje ich pliki).</li>
<li><strong>Gwarancja:</strong> okres (30-90 dni), w którym wykonawca naprawia błędy w tym, co zbudował - bez opłat. To nie to samo, co opieka nad stroną; aktualizacja wtyczek po pół roku nie jest naprawą błędu.</li>
<li><strong>Poufność:</strong> jeśli wykonawca dostaje dane klientów, cenniki czy dostęp do systemów - krótki punkt o poufności i RODO (powierzenie przetwarzania, jeśli faktycznie będzie przetwarzał dane osobowe).</li>
</ul>

<h2>Lista kontrolna przed podpisaniem</h2>

<ul>
<li>Jest lista podstron i funkcji - nie „strona internetowa".</li>
<li>Są etapy z terminami i czasem na akceptację.</li>
<li>Jest liczba rund poprawek i definicja poprawki.</li>
<li>Jest harmonogram płatności powiązany z etapami.</li>
<li>Jest przeniesienie praw autorskich z prawem do modyfikacji.</li>
<li>Domena i hosting są na klienta.</li>
<li>Jest okres gwarancji i jest powiedziane, co obejmuje.</li>
<li>Wiadomo, co umowa wyklucza.</li>
</ul>

<p>Jeśli wykonawca sam przychodzi z umową, która to wszystko zawiera - to dobry znak. Jeśli proponuje „zróbmy to bez papierów, szybciej będzie" - szybciej będzie tylko do pierwszego nieporozumienia.</p>

<p>Na Projekciarz.pl zasady współpracy i płatności ustalasz bezpośrednio z wykonawcą - platforma nie pośredniczy w umowie. Tym bardziej warto ją mieć. <a href="/announcements/create">Opisz swój projekt</a>, porównaj oferty i zanim wybierzesz, poproś o wzór umowy - reakcja na tę prośbę powie Ci sporo.</p>
HTML;
    }

    private function contentAcceptance(): string
    {
        return <<<'HTML'
<p>Moment odbioru strony to ostatnia chwila, w której masz pełną uwagę wykonawcy. Po przelaniu ostatniej transzy każda poprawka staje się „nowym zleceniem", a każde pytanie czeka w kolejce za innymi klientami. Dlatego warto poświęcić dwie godziny i przejść poniższą listę punkt po punkcie - najlepiej razem z kimś, kto strony nie widział na oczy.</p>

<p>Lista jest podzielona na obszary. Nie wszystko dotyczy każdego projektu - sklep ma więcej punktów niż wizytówka - ale lepiej odhaczyć „nie dotyczy" niż o czymś zapomnieć.</p>

<h2>Wygląd i działanie</h2>

<ol>
<li><strong>Przeklikaj każdą podstronę na telefonie.</strong> Nie w trybie „widok mobilny" w przeglądarce na komputerze, tylko na prawdziwym telefonie - najlepiej na dwóch: iPhonie i Androidzie. Ponad połowa ruchu na większości stron pochodzi z komórek.</li>
<li><strong>Sprawdź stronę w drugiej przeglądarce.</strong> Jeśli wykonawca pracuje na Chrome, obejrzyj stronę w Safari lub Firefoksie. Różnice w fontach i formularzach zdarzają się częściej, niż się wydaje.</li>
<li><strong>Kliknij każdy link w menu, stopce i treści.</strong> Szukasz linków prowadzących donikąd, na adres testowy wykonawcy albo na „#".</li>
<li><strong>Porównaj z zaakceptowanym projektem graficznym.</strong> Drobne różnice są normalne, ale zmienione kolory, inne fonty czy brakujące sekcje - nie.</li>
<li><strong>Zobacz, co się dzieje przy długich treściach.</strong> Wpisz w nazwę produktu lub nagłówek bardzo długi tekst i sprawdź, czy układ się nie rozsypuje. To samo z bardzo krótkimi.</li>
<li><strong>Sprawdź teksty zastępcze.</strong> Szukaj „Lorem ipsum", „Nagłówek tutaj", „Jan Kowalski" i numerów telefonu 123 456 789.</li>
</ol>

<h2>Formularze i funkcje</h2>

<ol start="7">
<li><strong>Wyślij każdy formularz naprawdę.</strong> Formularz kontaktowy, zapis do newslettera, zapytanie o wycenę - wypełnij i wyślij. Sprawdź, czy wiadomość dotarła na właściwy adres (nie na adres wykonawcy) i czy nie wylądowała w spamie.</li>
<li><strong>Sprawdź komunikaty.</strong> Co widzi użytkownik po wysłaniu? Co widzi, gdy pominie pole wymagane? Czy komunikaty są po polsku?</li>
<li><strong>W sklepie: złóż zamówienie testowe od początku do końca.</strong> Dodaj do koszyka, wybierz dostawę, zapłać testową kartą (bramki płatności mają tryb testowy), sprawdź e-mail z potwierdzeniem i to, czy zamówienie widać w panelu.</li>
<li><strong>Sprawdź, co się dzieje przy błędzie płatności.</strong> Odrzucona karta nie może kończyć się białą stroną.</li>
<li><strong>Wyszukiwarka, filtry, sortowanie</strong> - jeśli są, wpisz coś, czego na pewno nie ma, i coś z literówką.</li>
</ol>

<h2>Szybkość i technikalia</h2>

<ol start="12">
<li><strong>Przepuść stronę główną i jedną podstronę przez PageSpeed Insights.</strong> Nie chodzi o wynik 100. Chodzi o to, czy nie ma czerwonych ostrzeżeń o obrazkach ważących po kilka megabajtów - to najczęstszy i najłatwiejszy do naprawy problem.</li>
<li><strong>Certyfikat SSL.</strong> Kłódka w przeglądarce na każdej podstronie, a wejście przez <code>http://</code> przekierowuje na <code>https://</code>.</li>
<li><strong>Wersja z www i bez www</strong> prowadzą w to samo miejsce - jedna przekierowuje na drugą.</li>
<li><strong>Strona błędu 404.</strong> Wejdź na nieistniejący adres. Powinieneś zobaczyć stronę w szacie Twojej witryny, nie surowy komunikat serwera.</li>
<li><strong>Favicon</strong> (ikonka w karcie przeglądarki) jest Twoja, nie domyślna z szablonu.</li>
</ol>

<h2>SEO - minimum, bez którego Google Cię nie znajdzie</h2>

<ol start="17">
<li><strong>Tytuły i opisy podstron.</strong> Każda podstrona ma własny tytuł (widać go w karcie przeglądarki) - nie „Strona główna - Strona główna" na wszystkich.</li>
<li><strong>Strona nie jest zablokowana dla wyszukiwarek.</strong> Wersje testowe często mają włączoną blokadę indeksowania; po wdrożeniu musi być wyłączona. Zapytaj wprost: „czy zdjąłeś noindex?".</li>
<li><strong>Jest plik sitemap.xml i strona jest dodana do Google Search Console</strong> - na Twoje konto Google, nie wykonawcy.</li>
<li><strong>Jeśli to przebudowa starej strony:</strong> stare adresy przekierowują na nowe. Bez tego tracisz pozycje wypracowane latami. Poproś o listę przekierowań.</li>
</ol>

<h2>Dostępy, kopie i formalności</h2>

<ol start="21">
<li><strong>Masz komplet dostępów:</strong> panel strony (konto administratora na Twój e-mail), hosting, domena, poczta, Analytics, Search Console, bramka płatności. Zaloguj się do każdego i zmień hasła.</li>
<li><strong>Działają kopie zapasowe.</strong> Zapytaj, gdzie są, jak często się robią i - najważniejsze - poproś o pokazanie, jak przywrócić stronę z kopii. Kopia, której nikt nigdy nie przywracał, to kopia hipotetyczna.</li>
<li><strong>RODO i cookies:</strong> jest polityka prywatności z Twoimi danymi jako administratora, baner cookies (jeśli używasz Analytics lub pikseli reklamowych), a formularze mają zgody tam, gdzie są potrzebne.</li>
<li><strong>Masz pliki źródłowe</strong> - projekt graficzny (np. plik Figmy) i kod, jeśli umowa to przewiduje - oraz dowody zakupu licencji na motyw, wtyczki i zdjęcia.</li>
</ol>

<h2>Co zrobić z wynikiem</h2>

<p>Spisz wszystko, co nie działa, w jednej liście - z adresem podstrony, opisem i zrzutem ekranu. Jedna lista zamiast dwudziestu maili to coś, za co wykonawca będzie Ci wdzięczny i co szybciej doprowadzi do końca.</p>

<p>Rozróżnij przy tym trzy kategorie:</p>

<ul>
<li><strong>Błędy</strong> - coś, co miało działać i nie działa. Wykonawca poprawia w ramach umowy.</li>
<li><strong>Odstępstwa od projektu</strong> - różnice względem zaakceptowanych makiet. Też w ramach umowy.</li>
<li><strong>Nowe pomysły</strong> - rzeczy, które dopiero teraz przyszły Ci do głowy. To zmiana zakresu i uczciwie jest tak to nazwać.</li>
</ul>

<p>Ostatnią płatność przelej po tym, jak lista z dwóch pierwszych kategorii jest pusta - nie wcześniej, ale też nie przeciągaj tego z powodu trzeciej. Jeśli podpisałeś umowę z okresem gwarancji (o tym, dlaczego warto, piszemy w tekście <a href="/blog/umowa-z-freelancerem-na-strone-www">o umowie z freelancerem</a>), drobiazgi wykryte po odbiorze też są do naprawy bez dodatkowych kosztów.</p>

<p>A kiedy wszystko działa - wystaw wykonawcy ocenę. Na Projekciarz.pl opinie mogą dodawać tylko klienci po zakończonej współpracy, więc Twoja ocena realnie pomaga kolejnym osobom w wyborze.</p>
HTML;
    }

    private function contentTimeline(): string
    {
        return <<<'HTML'
<p>„Strona w 7 dni" i „sklep w 3 miesiące" to dwie liczby, które najczęściej pojawiają się w ofertach - i obie potrafią być prawdziwe. Różnica nie leży w szybkości wykonawcy, tylko w tym, ile pracy koncepcyjnej jest do zrobienia, ile integracji trzeba spiąć i jak szybko <em>Ty</em> odpowiadasz na pytania. Poniżej realistyczne widełki i rozbicie na etapy, żebyś wiedział, czego wymagać i czego się spodziewać.</p>

<h2>Realne widełki czasowe</h2>

<table>
<thead>
<tr><th>Typ projektu</th><th>Typowy czas</th><th>Co zakładamy</th></tr>
</thead>
<tbody>
<tr><td>Strona wizytówka (1-5 podstron)</td><td>1-3 tygodnie</td><td>Gotowy szablon, teksty i zdjęcia dostarczone na start</td></tr>
<tr><td>Strona firmowa (5-15 podstron)</td><td>4-8 tygodni</td><td>Indywidualny projekt graficzny, część tekstów do napisania</td></tr>
<tr><td>Sklep internetowy (do kilkuset produktów)</td><td>6-12 tygodni</td><td>Płatności, dostawy, import produktów, projekt indywidualny</td></tr>
<tr><td>Sklep z integracjami (ERP, hurtownie, wiele magazynów)</td><td>3-6 miesięcy</td><td>Integracje wymagają testów po obu stronach</td></tr>
<tr><td>Aplikacja webowa / portal</td><td>3 miesiące i więcej</td><td>Zależy od liczby funkcji; zwykle etapami</td></tr>
</tbody>
</table>

<p>Te liczby to czas <em>kalendarzowy</em>, nie czas pracy wykonawcy. Strona firmowa to może być 60-80 godzin roboczych rozłożonych na sześć tygodni - bo między etapami czeka się na decyzje, materiały i poprawki.</p>

<h2>Etapy i ile trwa każdy z nich</h2>

<h3>1. Brief i ustalenie zakresu: 2-5 dni</h3>

<p>Rozmowa lub ankieta, lista podstron, funkcje, przykłady stron, które Ci się podobają. Im lepiej przygotowany brief, tym krótszy ten etap - i tym mniej niespodzianek później. Dobrze opisane zlecenie potrafi skrócić go do jednego spotkania. Jak je napisać, pokazujemy w tekście <a href="/blog/jak-opisac-zlecenie-zeby-dostac-dobre-oferty">Jak opisać zlecenie, żeby dostać dobre oferty</a>.</p>

<h3>2. Makiety i projekt graficzny: 1-3 tygodnie</h3>

<p>Najpierw makiety (układ bez kolorów i grafik), potem projekt. Przy szablonie etap skraca się do wyboru i dopasowania kolorystyki - kilka dni. Przy projekcie indywidualnym najwięcej czasu zajmują rundy poprawek: każda to zwykle 2-4 dni (dzień na Twoją odpowiedź, dzień lub dwa na zmiany).</p>

<h3>3. Treści: równolegle, ale to one najczęściej blokują projekt</h3>

<p>Teksty i zdjęcia nie są „etapem" w harmonogramie wykonawcy, bo zwykle dostarcza je klient. W praktyce to najczęstsza przyczyna opóźnień: projekt graficzny jest gotowy, kodowanie skończone, a strona czeka miesiąc na opis oferty. Jeśli nie masz treści i nie masz czasu ich pisać - zleć je od razu, w ramach projektu albo osobno.</p>

<h3>4. Kodowanie i wdrożenie funkcji: 1-6 tygodni</h3>

<p>Wizytówka na szablonie to kilka dni. Strona firmowa z indywidualnym projektem - dwa do trzech tygodni. Sklep - od trzech tygodni wzwyż, bo oprócz wyglądu trzeba skonfigurować płatności, dostawy, podatki, e-maile transakcyjne i zaimportować produkty. Każda integracja z systemem zewnętrznym (fakturowanie, magazyn, kurierzy) dokłada od kilku dni do kilku tygodni - w zależności od tego, jak dobrze jest udokumentowana po drugiej stronie.</p>

<h3>5. Testy i poprawki: 3-10 dni</h3>

<p>Wykonawca testuje, Ty testujesz, powstaje lista uwag, wykonawca poprawia. Jedna runda zwykle wystarcza, jeśli wcześniejsze etapy były akceptowane na bieżąco. Co sprawdzić w tym momencie, rozpisaliśmy w <a href="/blog/odbior-strony-internetowej-checklista">checkliście odbioru strony</a>.</p>

<h3>6. Wdrożenie: 1-2 dni</h3>

<p>Przeniesienie na docelową domenę, SSL, przekierowania ze starej strony, zgłoszenie do Google. Krótki etap, ale warto go planować na początek tygodnia, nie na piątek po południu - jeśli coś nie zadziała, lepiej, żeby wykonawca był pod ręką.</p>

<h2>Cztery rzeczy, które wydłużają projekt najbardziej</h2>

<ol>
<li><strong>Brak treści.</strong> Opisane wyżej. Jeśli masz dać teksty - ustal termin i trzymaj się go, bo wykonawca w tym czasie bierze inne zlecenia i wraca do Ciebie, kiedy ma okno.</li>
<li><strong>Decyzje podejmowane przez komitet.</strong> Projekt graficzny oglądany przez pięć osób, z których każda ma uwagi, to trzy dodatkowe rundy poprawek. Wyznacz jedną osobę decyzyjną po swojej stronie.</li>
<li><strong>Zmiany zakresu w trakcie.</strong> „Skoro już robimy stronę, to może dołóżmy sklep" - to nie przedłużenie projektu, to nowy projekt. Lepiej spisać pomysły na „etap drugi" i wrócić do nich po wdrożeniu.</li>
<li><strong>Integracje z systemami, do których nikt nie ma dostępu.</strong> Jeśli sklep ma łączyć się z Twoim programem magazynowym, zanim ruszy projekt, sprawdź, czy ten program w ogóle ma API i kto po stronie dostawcy odpowiada na pytania.</li>
</ol>

<h2>Jak czytać termin w ofercie</h2>

<p>Gdy wykonawca pisze „4 tygodnie", zapytaj o trzy rzeczy: od czego liczony jest start (od zaliczki? od dostarczenia materiałów?), ile dni na akceptację ma klient na każdym etapie, i czy termin zakłada, że treści są gotowe. Oferta, która mówi „4 tygodnie od dostarczenia kompletu treści, przy maksymalnie 3 dniach na akceptację etapu", jest uczciwsza niż „4 tygodnie" - choć brzmi mniej atrakcyjnie.</p>

<p>Podejrzanie krótki termin przy indywidualnym projekcie (np. strona firmowa „w tydzień") zwykle oznacza jedno z dwóch: gotowy szablon z podmienionym logo albo wykonawcę, który dopiero zobaczy, ile pracy jest do zrobienia.</p>

<h2>Podsumowanie</h2>

<p>Realny harmonogram strony firmowej to 4-8 tygodni, sklepu 6-12, a większość odchyleń od tych liczb bierze się nie z tempa kodowania, tylko z czasu oczekiwania na decyzje i materiały. Jeśli chcesz skończyć w terminie, najwięcej zależy od tego, co przygotujesz <em>przed</em> startem: brief, treści i jedną osobę, która będzie podejmować decyzje.</p>

<p>Chcesz zobaczyć, jakie terminy proponują wykonawcy przy Twoim zakresie? <a href="/announcements/create">Opublikuj zlecenie na Projekciarz.pl</a> - każda oferta zawiera cenę i deklarowany czas realizacji, więc łatwo porównać, kto obiecuje realistycznie, a kto zbyt pięknie.</p>
HTML;
    }
}
