<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Wpis skierowany do klientów zlecających projekty - reszta bloga mówi
 * niemal wyłącznie do wykonawców.
 */
class BlogPostPricingSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', 'admin')->first();

        if (!$author) {
            $this->command?->warn('Brak konta admina - pomijam BlogPostPricingSeeder.');
            return;
        }

        $tagIds = [];
        foreach (['Poradnik', 'Wycena', 'Dla klientów', 'E-commerce', 'WordPress'] as $name) {
            $tag = Tag::updateOrCreate(
                ['name' => $name, 'type' => 'blog'],
                ['slug' => Str::slug($name), 'type' => 'blog'],
            );
            $tagIds[] = $tag->id;
        }

        $post = BlogPost::updateOrCreate(
            ['slug' => 'ile-kosztuje-strona-internetowa-2026'],
            [
                'author_id' => $author->id,
                'category_id' => Category::where('slug', 'strona-firmowa')->value('id'),
                'title' => 'Ile kosztuje strona internetowa w 2026? Widełki cenowe i co wpływa na wycenę',
                'excerpt' => 'Wizytówka, strona firmowa, sklep czy aplikacja - realne widełki cenowe na polskim rynku, siedem czynników, które podbijają wycenę, oraz koszty, o których wykonawcy często nie wspominają w ofercie.',
                'meta_title' => 'Ile kosztuje strona internetowa w 2026? Widełki cenowe',
                'meta_description' => 'Realne widełki cenowe stron internetowych w Polsce: wizytówka, strona firmowa, sklep i aplikacja. Co podbija wycenę i jak porównać oferty wykonawców.',
                'meta_keywords' => [
                    'ile kosztuje strona internetowa',
                    'cennik stron internetowych',
                    'wycena strony www',
                    'koszt sklepu internetowego',
                    'ile kosztuje sklep internetowy',
                    'wycena projektu www',
                ],
                'content' => $this->content(),
                'status' => 'published',
                'published_at' => now(),
            ],
        );

        $post->tags()->syncWithoutDetaching($tagIds);

        $this->command?->info('Wpis opublikowany: /blog/' . $post->slug);
    }

    private function content(): string
    {
        return <<<'HTML'
<p>Na pytanie „ile kosztuje strona internetowa" najczęstszą odpowiedzią jest „to zależy". To prawda, ale kompletnie bezużyteczna, jeśli dopiero planujesz budżet. Poniżej znajdziesz konkretne widełki, listę czynników, które realnie przesuwają cenę, oraz koszty, o których łatwo zapomnieć przy podpisywaniu umowy.</p>

<h2>Widełki cenowe według typu projektu</h2>

<p>To orientacyjne przedziały dla polskiego rynku. Górna granica to zwykle praca zespołu z dedykowanym projektem graficznym, dolna - jeden wykonawca pracujący na gotowym motywie.</p>

<table>
<thead>
<tr><th>Typ projektu</th><th>Widełki</th><th>Czas realizacji</th></tr>
</thead>
<tbody>
<tr><td>Wizytówka / one-page</td><td>1 500 – 4 000 zł</td><td>1–2 tygodnie</td></tr>
<tr><td>Landing page sprzedażowy</td><td>2 000 – 8 000 zł</td><td>1–3 tygodnie</td></tr>
<tr><td>Strona firmowa (5–10 podstron)</td><td>4 000 – 12 000 zł</td><td>3–6 tygodni</td></tr>
<tr><td>WordPress z rozbudowaną personalizacją</td><td>3 000 – 9 000 zł</td><td>2–5 tygodni</td></tr>
<tr><td>Redesign istniejącej strony</td><td>3 000 – 15 000 zł</td><td>2–6 tygodni</td></tr>
<tr><td>Sklep internetowy (WooCommerce)</td><td>8 000 – 30 000 zł</td><td>4–10 tygodni</td></tr>
<tr><td>Sklep z dużym katalogiem i integracjami</td><td>30 000 zł i więcej</td><td>2–6 miesięcy</td></tr>
<tr><td>Aplikacja webowa (MVP)</td><td>25 000 – 120 000 zł</td><td>2–6 miesięcy</td></tr>
</tbody>
</table>

<p><strong>Jak czytać te liczby:</strong> jeśli wszystkie oferty, które dostajesz, leżą znacząco poniżej dolnej granicy, to nie znaczy, że trafiłeś na okazję. Znaczy zwykle, że wykonawcy zrozumieli zakres inaczej niż Ty.</p>

<h2>Siedem rzeczy, które realnie podbijają wycenę</h2>

<h3>1. Liczba unikalnych widoków, a nie podstron</h3>
<p>Trzydzieści podstron zbudowanych z tego samego szablonu to jeden widok do zaprojektowania. Pięć podstron, z których każda wygląda inaczej, to pięć. Wyceniając, wykonawca liczy układy, nie pozycje w menu.</p>

<h3>2. Projekt graficzny od zera kontra gotowy motyw</h3>
<p>Indywidualny projekt w Figmie to zwykle 30–50% kosztu całego przedsięwzięcia. Gotowy motyw obcina tę część niemal do zera, ale zamyka Cię w cudzych decyzjach - i w przyszłości potrafi utrudnić rozbudowę.</p>

<h3>3. Integracje z systemami zewnętrznymi</h3>
<p>Formularz kontaktowy to godzina pracy. Podpięcie systemu magazynowego, płatności cyklicznych, kuriera i faktur to często tygodnie - zwłaszcza gdy dokumentacja API jest uboga albo system jest polskim rozwiązaniem niszowym.</p>

<h3>4. Treści i migracja</h3>
<p>Przeniesienie 300 wpisów z bloga razem ze zdjęciami, przekierowaniami i strukturą adresów to osobne zadanie, nie „przy okazji". Jeśli treści dopiero powstaną, ktoś musi je napisać - i to też jest pozycja w budżecie.</p>

<h3>5. Wielojęzyczność</h3>
<p>Druga wersja językowa to nie „przetłumaczenie tekstów". To osobna struktura adresów, przełącznik języka, tłumaczenia elementów interfejsu i oddzielna konfiguracja SEO. Realnie 20–40% narzutu.</p>

<h3>6. Termin</h3>
<p>Projekt na wczoraj oznacza, że wykonawca musi przesunąć inne zlecenia. Dopłata za priorytet w wysokości 20–30% jest normą, nie próbą naciągnięcia.</p>

<h3>7. Zakres opieki po wdrożeniu</h3>
<p>„Cena zawiera 12 miesięcy wsparcia" i „cena zawiera dwa tygodnie na poprawki" to dwie zupełnie różne oferty, nawet jeśli kwota na dole jest identyczna.</p>

<h2>Koszty, których nie ma w wycenie, a i tak je poniesiesz</h2>

<p>To najczęstsze źródło rozczarowania kilka miesięcy po wdrożeniu. Poniższe pozycje rzadko wchodzą w kwotę za wykonanie strony.</p>

<table>
<thead>
<tr><th>Pozycja</th><th>Koszt</th><th>Uwagi</th></tr>
</thead>
<tbody>
<tr><td>Domena .pl</td><td>60 – 120 zł / rok</td><td>Pierwszy rok bywa promocyjny, kolejne już nie</td></tr>
<tr><td>Hosting współdzielony</td><td>200 – 600 zł / rok</td><td>Wystarczy dla strony wizytówkowej</td></tr>
<tr><td>Serwer VPS</td><td>600 – 3 000 zł / rok</td><td>Potrzebny przy sklepie lub aplikacji</td></tr>
<tr><td>Certyfikat SSL</td><td>0 zł</td><td>Let's Encrypt; płatny SSL to dziś rzadko konieczność</td></tr>
<tr><td>Licencje wtyczek i motywu</td><td>200 – 1 500 zł / rok</td><td>Odnawialne - po wygaśnięciu brak aktualizacji</td></tr>
<tr><td>Opieka techniczna</td><td>150 – 800 zł / mies.</td><td>Aktualizacje, kopie zapasowe, monitoring</td></tr>
</tbody>
</table>

<p>Przy stronie firmowej realny koszt utrzymania to zwykle 1 500 – 5 000 zł rocznie. Warto wpisać to do budżetu od razu, a nie odkrywać przy pierwszej fakturze za hosting.</p>

<h2>Jak przygotować się, żeby oferty dało się porównać</h2>

<p>Największy problem przy zbieraniu wycen nie polega na tym, że są drogie - tylko na tym, że dotyczą różnych rzeczy. Zanim wyślesz zapytanie, przygotuj cztery informacje:</p>

<ul>
<li><strong>Cel biznesowy.</strong> „Chcę zbierać zapytania ofertowe" prowadzi do innego projektu niż „chcę sprzedawać online".</li>
<li><strong>Lista podstron z zaznaczeniem, które wyglądają inaczej.</strong> To ona decyduje o pracochłonności.</li>
<li><strong>Stan treści.</strong> Są gotowe, do przeniesienia, czy trzeba je napisać?</li>
<li><strong>Budżet - choćby jako widełki.</strong> Podanie przedziału nie osłabia Twojej pozycji. Pozwala wykonawcy zaproponować zakres, który się w nim mieści, zamiast zgadywać.</li>
</ul>

<p>Do tego dwa lub trzy linki do stron, które Ci się podobają, z krótkim uzasadnieniem czego dokładnie. „Podoba mi się ta prostota" niesie więcej informacji niż dziesięć zdań opisu.</p>

<h2>Czerwone flagi w wycenach</h2>

<ul>
<li><strong>Cena bez pytań.</strong> Konkretna kwota podana bez ani jednego pytania o zakres oznacza, że wykonawca policzył coś innego niż to, czego potrzebujesz.</li>
<li><strong>Brak informacji o prawach do projektu.</strong> Ustal na piśmie, czy po zapłacie kod i pliki źródłowe są Twoje.</li>
<li><strong>Brak zapisu o liczbie rund poprawek.</strong> To najczęstsza przyczyna sporów na końcu projektu.</li>
<li><strong>Dostępy tylko po stronie wykonawcy.</strong> Domena i hosting powinny być zarejestrowane na Ciebie, nie na agencję.</li>
<li><strong>Wycena dużo poniżej rynku.</strong> Zwykle kończy się dopłatami w trakcie albo porzuceniem projektu w połowie.</li>
</ul>

<h2>Podsumowanie</h2>

<p>Cena strony internetowej to funkcja trzech rzeczy: liczby unikalnych widoków, liczby integracji i tego, ile pracy koncepcyjnej zostaje po stronie wykonawcy. Im więcej z tego rozstrzygniesz przed wysłaniem zapytania, tym węższe i uczciwsze będą oferty, które dostaniesz.</p>

<p>Najprostszy sposób na sprawdzenie realnego poziomu cen to zebranie kilku ofert na ten sam, dobrze opisany zakres. <a href="/announcements/create">Opublikuj zlecenie na Projekciarz.pl</a> - opis zajmuje kilka minut, a oferty od wykonawców przychodzą razem z ich portfolio i ocenami od poprzednich klientów.</p>
HTML;
    }
}
