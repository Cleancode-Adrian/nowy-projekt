<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Dwa poradniki dla zleceniodawców: wybór technologii (WordPress czy strona
 * dedykowana) oraz koszty utrzymania strony po wdrożeniu. Każdy ma własną,
 * inną datę publikacji. Seeder jest idempotentny:
 * php artisan db:seed --class=BlogPostTechAndMaintenanceSeeder
 */
class BlogPostTechAndMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', 'admin')->first();

        if (!$author) {
            $this->command?->warn('Brak konta admina - pomijam BlogPostTechAndMaintenanceSeeder.');
            return;
        }

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
                    // Przy ponownym uruchomieniu data publikacji zostaje bez zmian.
                    'published_at' => $existing?->published_at ?? $data['published_at'],
                ],
            );

            $post->tags()->syncWithoutDetaching($tagIds);
            $this->command?->info('Wpis opublikowany: /blog/' . $post->slug . ' (' . $post->published_at->format('Y-m-d') . ')');
        }
    }

    private function posts(): array
    {
        return [
            [
                'slug' => 'wordpress-czy-strona-dedykowana',
                'category' => 'wordpress',
                'published_at' => now()->subDays(16)->setTime(9, 30),
                'title' => 'WordPress czy strona dedykowana? Jak wybrać technologię, której nie będziesz żałować za dwa lata',
                'excerpt' => 'Wykonawcy polecają zwykle to, w czym sami robią. Ten tekst patrzy od strony klienta: kto będzie dodawał treści, ile integracji potrzebujesz, kto zajmie się aktualizacjami i co się stanie, gdy zmienisz wykonawcę. Plus tabela porównawcza i cztery pytania, które rozstrzygają wybór.',
                'meta_title' => 'WordPress czy strona dedykowana? Jak wybrać technologię',
                'meta_description' => 'WordPress, kreator (Wix, Webflow) czy strona kodowana od zera - porównanie kosztów, utrzymania, elastyczności i ryzyka uzależnienia od wykonawcy. Jak wybrać technologię strony firmowej lub sklepu.',
                'meta_keywords' => ['wordpress czy strona dedykowana', 'w jakiej technologii zrobić stronę', 'wordpress czy webflow', 'strona dedykowana czy cms', 'technologia strony internetowej'],
                'image_alt' => 'WordPress czy strona dedykowana - jak wybrać technologię strony internetowej',
                'tags' => ['Dla klientów', 'Poradnik', 'WordPress', 'Web Development'],
                'content' => $this->contentTechnology(),
            ],
            [
                'slug' => 'ile-kosztuje-utrzymanie-strony-internetowej',
                'category' => 'maintenance',
                'published_at' => now()->subDays(12)->setTime(10, 15),
                'title' => 'Ile kosztuje utrzymanie strony internetowej? Domena, hosting, aktualizacje i opieka - realne kwoty rocznie',
                'excerpt' => 'Cena wykonania strony to jedno, ale co potem? Zestawienie stałych kosztów od wizytówki po sklep: co jest obowiązkowe, co opcjonalne, gdzie firmy przepłacają, a gdzie oszczędzają tak, że kończy się to włamaniem albo utratą domeny.',
                'meta_title' => 'Ile kosztuje utrzymanie strony internetowej? Koszty roczne',
                'meta_description' => 'Roczne koszty utrzymania strony WWW i sklepu: domena, hosting, certyfikat SSL, aktualizacje WordPressa, kopie zapasowe, opieka techniczna, poczta. Widełki cenowe i na czym nie oszczędzać.',
                'meta_keywords' => ['ile kosztuje utrzymanie strony internetowej', 'koszt utrzymania strony www rocznie', 'opieka nad stroną wordpress cena', 'hosting i domena koszt', 'koszty utrzymania sklepu internetowego'],
                'image_alt' => 'Ile kosztuje utrzymanie strony internetowej - domena, hosting, aktualizacje, opieka',
                'tags' => ['Dla klientów', 'Poradnik', 'Wycena'],
                'content' => $this->contentMaintenance(),
            ],
        ];
    }

    private function contentTechnology(): string
    {
        return <<<'HTML'
<p>„W czym zrobić stronę?" to pytanie, na które każdy wykonawca odpowie inaczej - i prawie zawsze wskaże technologię, w której sam pracuje. To nie jest nieuczciwość, tylko naturalna perspektywa. Problem w tym, że wybór technologii odczujesz nie w dniu wdrożenia, tylko przez kolejne lata: przy każdej zmianie treści, każdej aktualizacji i każdej zmianie wykonawcy. Ten tekst ma pomóc Ci wybrać z perspektywy klienta, nie programisty.</p>

<h2>Trzy drogi, które realnie masz do wyboru</h2>

<h3>1. WordPress (lub inny CMS)</h3>

<p>Gotowy system zarządzania treścią, na którym działa mniej więcej 40% stron na świecie. Wykonawca dobiera motyw (szablon) lub projektuje własny, dokłada wtyczki (formularze, SEO, sklep) i oddaje Ci panel, w którym sam dodajesz wpisy, zmieniasz teksty i zdjęcia.</p>

<ul>
<li><strong>Plusy:</strong> samodzielna edycja treści, ogromny wybór wtyczek, tysiące wykonawców - łatwo znaleźć kogoś do przejęcia strony.</li>
<li><strong>Minusy:</strong> wymaga regularnych aktualizacji (nieaktualizowany WordPress to najczęstszy powód włamań), bywa wolny przy nadmiarze wtyczek, a „tanie" wdrożenia na kupnym motywie wyglądają jak tysiąc innych stron.</li>
</ul>

<h3>2. Kreator stron (Wix, Squarespace, Webflow, Shopify dla sklepów)</h3>

<p>Platforma w abonamencie, w której strona powstaje w edytorze wizualnym. Hosting, aktualizacje i bezpieczeństwo są po stronie dostawcy - Ty płacisz miesięcznie i nie myślisz o technikaliach.</p>

<ul>
<li><strong>Plusy:</strong> zero utrzymania technicznego, szybki start, przewidywalny koszt miesięczny.</li>
<li><strong>Minusy:</strong> strona „mieszka" u dostawcy - nie przeniesiesz jej na inny hosting, a nietypowe funkcje albo są niemożliwe, albo kosztują osobno. Abonament rośnie wraz z potrzebami (wersje językowe, sklep, formularze zaawansowane).</li>
</ul>

<h3>3. Strona dedykowana (kodowana od zera lub na frameworku)</h3>

<p>Wykonawca pisze stronę pod Twoje wymagania - w Laravelu, Next.js, Astro czy innym narzędziu. Panel do edycji treści powstaje tylko wtedy, gdy go zamówisz, i obejmuje tylko to, co w nim zaplanowano.</p>

<ul>
<li><strong>Plusy:</strong> pełna kontrola nad wyglądem i wydajnością, brak zbędnego kodu, nietypowe integracje (CRM, systemy rezerwacji, API hurtowni) bez kombinowania z wtyczkami.</li>
<li><strong>Minusy:</strong> wyższy koszt startowy, każda zmiana funkcji wymaga programisty, a przejęcie strony przez innego wykonawcę zależy od jakości kodu i dokumentacji, której często nie ma.</li>
</ul>

<h2>Porównanie w jednej tabeli</h2>

<table>
<thead>
<tr><th>Kryterium</th><th>WordPress</th><th>Kreator</th><th>Dedykowana</th></tr>
</thead>
<tbody>
<tr><td>Koszt wdrożenia (strona firmowa)</td><td>średni</td><td>niski</td><td>wysoki</td></tr>
<tr><td>Koszt utrzymania rocznie</td><td>średni (hosting + aktualizacje)</td><td>abonament, rośnie z funkcjami</td><td>niski-średni, ale zmiany wymagają programisty</td></tr>
<tr><td>Samodzielna edycja treści</td><td>pełna</td><td>pełna</td><td>tylko to, co przewidziano w panelu</td></tr>
<tr><td>Nietypowe integracje</td><td>wtyczki lub kod na zamówienie</td><td>ograniczone</td><td>bez ograniczeń</td></tr>
<tr><td>Szybkość działania</td><td>zależy od wdrożenia</td><td>dobra</td><td>najlepsza</td></tr>
<tr><td>Zmiana wykonawcy</td><td>łatwa</td><td>łatwa w obrębie platformy, trudna poza nią</td><td>zależy od jakości kodu</td></tr>
<tr><td>Przeniesienie na inny hosting</td><td>tak</td><td>nie</td><td>tak</td></tr>
</tbody>
</table>

<h2>Cztery pytania, które rozstrzygają wybór</h2>

<h3>Kto będzie dodawał treści i jak często?</h3>

<p>Jeśli planujesz blog, aktualności, zmiany w ofercie raz w tygodniu - potrzebujesz wygodnego panelu, czyli WordPressa albo kreatora. Jeśli strona ma się zmieniać dwa razy w roku, panel jest zbędnym kosztem, a dedykowana strona bez CMS będzie szybsza i bezpieczniejsza.</p>

<h3>Czego strona ma „nie umieć"?</h3>

<p>Zrób listę funkcji na dziś i na dwa lata do przodu: wersje językowe, rezerwacje online, strefa klienta, integracja z programem fakturowym, konfigurator produktu. Jeśli na liście są rzeczy, których nie ma w standardowych wtyczkach, kreator odpada, a WordPress zaczyna wymagać kodu na zamówienie - czyli zbliża się kosztem do strony dedykowanej.</p>

<h3>Kto zajmie się aktualizacjami?</h3>

<p>WordPress wymaga ich co kilka tygodni. Jeśli nie masz w firmie nikogo, kto to ogarnie, dolicz opiekę techniczną do kosztów - albo wybierz kreator, gdzie to problem dostawcy. Więcej o kwotach w tekście <a href="/blog/ile-kosztuje-utrzymanie-strony-internetowej">Ile kosztuje utrzymanie strony internetowej</a>.</p>

<h3>Co się stanie, gdy rozstaniesz się z wykonawcą?</h3>

<p>To pytanie, którego prawie nikt nie zadaje na starcie, a które decyduje o wszystkim po dwóch latach. Przy WordPressie znajdziesz kolejnego wykonawcę w tydzień. Przy kreatorze - też, ale nigdy nie wyniesiesz strony poza platformę. Przy stronie dedykowanej zapytaj wprost: czy dostanę kod, dokumentację i instrukcję wdrożenia? Jeśli odpowiedź jest wymijająca, wróć do pytań z poradnika <a href="/blog/jak-wybrac-wykonawce-strony-internetowej">Jak wybrać wykonawcę strony internetowej</a>.</p>

<h2>Typowe scenariusze</h2>

<ul>
<li><strong>Wizytówka lokalnej firmy, 5-8 podstron, zmiany kilka razy w roku</strong> - WordPress na lekkim motywie albo kreator. Dedykowana strona to tu przepłacanie.</li>
<li><strong>Firma usługowa z blogiem i ofertą, która żyje</strong> - WordPress z własnym projektem graficznym. Panel, który znasz, i wykonawcy na każdym rogu.</li>
<li><strong>Sklep do kilkuset produktów bez nietypowych procesów</strong> - WooCommerce (WordPress) lub Shopify. Wybór zależy od tego, czy wolisz własny hosting i kontrolę, czy abonament i spokój.</li>
<li><strong>Sklep z integracją z hurtownią, ERP, indywidualną logiką cen</strong> - dedykowany lub mocno rozbudowany system. Tu wtyczki zaczynają się gryźć, a każdy miesiąc łatania kosztuje więcej niż porządne wdrożenie.</li>
<li><strong>Aplikacja, strefa klienta, portal</strong> - tylko dedykowana. To nie jest „strona", to oprogramowanie, i tak trzeba to wycenić.</li>
</ul>

<h2>Na koniec: o co poprosić w ofercie</h2>

<p>Niezależnie od technologii poproś wykonawcę, żeby w ofercie zapisał <em>dlaczego</em> proponuje właśnie tę - w odniesieniu do Twoich odpowiedzi na cztery pytania powyżej. Uzasadnienie „bo w tym robimy" jest sygnałem, że wybór jest wygodny dla wykonawcy, niekoniecznie dla Ciebie.</p>

<p>Jeśli chcesz zobaczyć, jak różni wykonawcy podchodzą do tego samego projektu, <a href="/announcements/create">opublikuj zlecenie na Projekciarz.pl</a> i w opisie poproś o rekomendację technologii z uzasadnieniem. Różnice w odpowiedziach powiedzą Ci więcej niż niejedno portfolio.</p>
HTML;
    }

    private function contentMaintenance(): string
    {
        return <<<'HTML'
<p>Większość wycen kończy się na kwocie za wykonanie strony. Tymczasem strona generuje koszty co miesiąc i co rok - od oczywistych (domena, hosting) po te, o których dowiadujesz się dopiero po włamaniu albo po tym, jak formularz kontaktowy przestał działać pół roku temu i nikt tego nie zauważył. Poniżej pełna lista z realnymi widełkami, żebyś mógł zaplanować budżet na cały cykl życia strony, nie tylko na start.</p>

<h2>Koszty obowiązkowe - bez nich strona nie istnieje</h2>

<h3>Domena: 50-150 zł rocznie</h3>

<p>Domena .pl kosztuje zwykle kilkadziesiąt złotych za pierwszy rok (promocja) i 80-150 zł przy odnowieniu. Domeny .com są w podobnym przedziale. Trzy rzeczy, które mają znaczenie:</p>

<ul>
<li>Domena musi być zarejestrowana <strong>na Ciebie</strong> (Twoje dane, Twój e-mail), nie na wykonawcę.</li>
<li>Włącz automatyczne odnawianie i podepnij kartę, która nie wygaśnie. Wygasła domena to strona, poczta i wizytówki, które przestają działać jednego dnia.</li>
<li>Nie kupuj domeny w pakiecie z hostingiem u przypadkowej firmy „bo taniej" - przeniesienie potem bywa uciążliwe.</li>
</ul>

<h3>Hosting: 150-1500 zł rocznie</h3>

<p>Przedział jest szeroki, bo zależy od tego, co hostujesz:</p>

<table>
<thead>
<tr><th>Rodzaj strony</th><th>Typ hostingu</th><th>Koszt rocznie</th></tr>
</thead>
<tbody>
<tr><td>Wizytówka, strona firmowa</td><td>hosting współdzielony</td><td>150-400 zł</td></tr>
<tr><td>Strona z blogiem, większy ruch</td><td>lepszy hosting współdzielony / hosting pod WordPressa</td><td>300-800 zł</td></tr>
<tr><td>Sklep internetowy</td><td>hosting pod WooCommerce lub VPS</td><td>600-1500 zł</td></tr>
<tr><td>Aplikacja, strefa klienta</td><td>VPS lub chmura</td><td>od 1000 zł wzwyż</td></tr>
</tbody>
</table>

<p>Najtańszy hosting za 60 zł rocznie zwykle oznacza wolną stronę i brak kopii zapasowych. Różnica 200 zł rocznie w stosunku do czasu ładowania i stabilności jest najlepiej wydanymi pieniędzmi na tej liście.</p>

<h3>Certyfikat SSL: 0 zł (zwykle)</h3>

<p>Kłódka w przeglądarce. Większość hostingów daje darmowy certyfikat Let's Encrypt z automatycznym odnawianiem. Jeśli ktoś proponuje Ci certyfikat za 200-500 zł rocznie dla zwykłej strony firmowej, zapytaj dlaczego - w 95% przypadków nie ma powodu.</p>

<h2>Koszty, które „można pominąć" - do czasu</h2>

<h3>Aktualizacje i opieka techniczna: 0-3600 zł rocznie</h3>

<p>Dotyczy głównie WordPressa i innych CMS-ów. System, motyw i wtyczki wymagają aktualizacji co kilka tygodni. Bez tego strona staje się celem automatycznych ataków - nie dlatego, że ktoś się na Ciebie uwziął, tylko dlatego, że boty skanują internet w poszukiwaniu znanych dziur.</p>

<ul>
<li><strong>Robisz sam:</strong> 0 zł, około 30 minut miesięcznie, pod warunkiem że wiesz, co zrobić, gdy aktualizacja coś zepsuje.</li>
<li><strong>Pakiet opieki u wykonawcy:</strong> 100-300 zł miesięcznie za stronę firmową, 300-800 zł za sklep. W cenie zwykle: aktualizacje, kopie zapasowe, monitoring dostępności, drobne poprawki (często limit 1-2 godzin miesięcznie).</li>
</ul>

<p>Zasada praktyczna: jeśli strona przynosi klientów, opieka techniczna jest ubezpieczeniem, nie kosztem. Jeśli strona to wizytówka, którą i tak nikt nie odwiedza, możesz zaryzykować - ale wtedy rozważ kreator stron, gdzie aktualizacje nie są Twoim problemem. Porównanie technologii pod tym kątem znajdziesz w tekście <a href="/blog/wordpress-czy-strona-dedykowana">WordPress czy strona dedykowana</a>.</p>

<h3>Kopie zapasowe: 0-300 zł rocznie</h3>

<p>Dobry hosting robi je automatycznie i trzyma przez 7-30 dni. Sprawdź to <em>przed</em> wykupieniem, nie po awarii. Jeśli hosting ich nie oferuje, wtyczka do backupu z zapisem na zewnętrzny dysk (Google Drive, Dropbox) kosztuje 0-300 zł rocznie w zależności od wersji.</p>

<h3>Poczta firmowa: 0-600 zł rocznie na skrzynkę</h3>

<p>Skrzynki na hostingu są zwykle w cenie, ale bywają zawodne (maile wpadają do spamu u odbiorców). Google Workspace lub Microsoft 365 to około 30-50 zł miesięcznie za użytkownika i znacznie lepsza dostarczalność. Dla firmy, która żyje z maili od klientów, to nie jest miejsce na oszczędności.</p>

<h2>Koszty zależne od tego, co strona robi</h2>

<ul>
<li><strong>Wtyczki i narzędzia premium</strong> (formularze zaawansowane, SEO, kreator stron, wielojęzyczność): 200-1500 zł rocznie. Każda licencja wygasa i wymaga odnowienia - zapytaj wykonawcę o listę przy odbiorze.</li>
<li><strong>Sklep:</strong> prowizje operatora płatności (zwykle 1-2% od transakcji), integracje z kurierami, dodatkowe moduły. Łącznie od kilkuset do kilku tysięcy złotych rocznie w zależności od obrotu.</li>
<li><strong>Narzędzia zewnętrzne:</strong> newsletter (od 0 do kilkuset zł miesięcznie wraz z rozmiarem bazy), system rezerwacji, chat, CRM.</li>
<li><strong>Zmiany i rozwój:</strong> nowa podstrona, zmiana oferty, odświeżenie grafiki. Rozliczane godzinowo (100-200 zł/h u freelancera, więcej w agencji) lub w ramach pakietu opieki.</li>
</ul>

<h2>Ile to wychodzi łącznie?</h2>

<table>
<thead>
<tr><th>Scenariusz</th><th>Minimum (sam ogarniasz)</th><th>Wygodnie (z opieką)</th></tr>
</thead>
<tbody>
<tr><td>Wizytówka na WordPressie</td><td>250-500 zł / rok</td><td>1500-3500 zł / rok</td></tr>
<tr><td>Strona firmowa z blogiem</td><td>500-1000 zł / rok</td><td>2500-5000 zł / rok</td></tr>
<tr><td>Sklep internetowy (bez prowizji od sprzedaży)</td><td>1500-3000 zł / rok</td><td>5000-12000 zł / rok</td></tr>
<tr><td>Strona w kreatorze (Wix, Webflow)</td><td colspan="2">800-3000 zł / rok w abonamencie, bez osobnej opieki</td></tr>
</tbody>
</table>

<p>Kwoty orientacyjne, stan na jesień 2026, bez kosztów reklamy i treści. Dla porównania koszt samego wykonania opisaliśmy w tekście <a href="/blog/ile-kosztuje-strona-internetowa-2026">Ile kosztuje strona internetowa w 2026</a>.</p>

<h2>Gdzie firmy przepłacają, a gdzie oszczędzają za bardzo</h2>

<p><strong>Przepłacają na:</strong> płatnym SSL dla zwykłej strony, hostingu „dedykowanym" dla wizytówki, pakiecie opieki, z którego nigdy nie korzystają, i wtyczkach premium kupionych „na zapas".</p>

<p><strong>Oszczędzają za bardzo na:</strong> hostingu za 60 zł (wolna strona = mniej klientów), braku aktualizacji (włamanie = koszt czyszczenia 1000-3000 zł plus wstyd), domenie zarejestrowanej na wykonawcę (utrata kontroli), kopiach zapasowych (awaria = strona od nowa).</p>

<h2>Co zrobić przed podpisaniem umowy na stronę</h2>

<ol>
<li>Poproś wykonawcę o <strong>listę wszystkich kosztów cyklicznych</strong> z kwotami i terminami odnowienia. Niech to będzie załącznik do oferty.</li>
<li>Ustal, <strong>kto odnawia</strong> domenę, hosting i licencje - Ty czy wykonawca - i na czyje konto są zarejestrowane.</li>
<li>Zdecyduj o opiece technicznej <strong>od razu</strong>, nie „zobaczymy po wdrożeniu". Po wdrożeniu wykonawca ma już kolejny projekt.</li>
<li>Przy odbiorze sprawdź dostępy i kopie zapasowe według <a href="/blog/odbior-strony-internetowej-checklista">checklisty odbioru strony</a>.</li>
</ol>

<p>Jeśli dopiero szukasz wykonawcy, <a href="/announcements/create">opublikuj zlecenie na Projekciarz.pl</a> i wpisz w opisie, że oczekujesz w ofercie zestawienia kosztów utrzymania. Wykonawcy, którzy je podadzą bez proszenia drugi raz, zwykle są też tymi, z którymi współpraca po wdrożeniu układa się najlepiej.</p>
HTML;
    }
}
