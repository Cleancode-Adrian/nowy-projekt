<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Drugi wpis dla zleceniodawców. Lepiej opisane zlecenia to trafniejsze oferty,
 * więc ten tekst pracuje zarówno na ruch z wyszukiwarki, jak i na jakość
 * ogłoszeń publikowanych na platformie.
 */
class BlogPostBriefSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', 'admin')->first();

        if (!$author) {
            $this->command?->warn('Brak konta admina - pomijam BlogPostBriefSeeder.');
            return;
        }

        $tagIds = [];
        foreach (['Poradnik', 'Dla klientów', 'Wycena', 'Web Development'] as $name) {
            $tag = Tag::updateOrCreate(
                ['name' => $name, 'type' => 'blog'],
                ['slug' => Str::slug($name), 'type' => 'blog'],
            );
            $tagIds[] = $tag->id;
        }

        $post = BlogPost::updateOrCreate(
            ['slug' => 'jak-opisac-zlecenie-zeby-dostac-dobre-oferty'],
            [
                'author_id' => $author->id,
                'category_id' => Category::where('slug', 'strona-firmowa')->value('id'),
                'title' => 'Jak opisać zlecenie, żeby dostać dobre oferty (a nie same pytania)',
                'excerpt' => 'Dobrze opisane zlecenie skraca drogę do sensownej wyceny i odsiewa przypadkowe zgłoszenia. Sześć elementów, które warto zawrzeć, gotowy szablon i najczęstsze błędy w opisach.',
                'meta_title' => 'Jak opisać zlecenie, żeby dostać dobre oferty',
                'meta_description' => 'Sześć elementów dobrego opisu zlecenia, gotowy szablon do skopiowania i błędy, przez które wykonawcy zadają dziesiątki pytań zamiast wysłać wycenę.',
                'meta_keywords' => [
                    'jak opisać zlecenie',
                    'brief projektowy',
                    'zlecenie strona internetowa',
                    'zapytanie ofertowe strona www',
                    'jak zamówić stronę internetową',
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
<p>Ogłoszenie „potrzebuję strony internetowej dla firmy" dostanie oferty od 2 000 do 25 000 zł. Nie dlatego, że wykonawcy są nieuczciwi - po prostu każdy wyobraził sobie inny projekt. Poniżej znajdziesz sześć elementów, które zamieniają takie ogłoszenie w opis dający porównywalne wyceny.</p>

<h2>Dlaczego to się opłaca</h2>

<p>Opis zlecenia pełni trzy funkcje naraz. Po pierwsze, pozwala wykonawcy policzyć pracę zamiast zgadywać - a przy zgadywaniu każdy dolicza zapas na ryzyko. Po drugie, odsiewa osoby, które nie robią tego typu projektów. Po trzecie, staje się punktem odniesienia, gdy pojawia się spór, czy coś było w zakresie.</p>

<p>Kwadrans na opis zwykle zwraca się w postaci węższych widełek i mniejszej liczby pytań w wiadomościach.</p>

<h2>Sześć elementów dobrego opisu</h2>

<h3>1. Cel, nie lista funkcji</h3>
<p>Zacznij od tego, co ma się wydarzyć po uruchomieniu strony. „Chcę, żeby klienci zostawiali zapytania o wycenę" prowadzi do innego projektu niż „chcę sprzedawać kursy online". Doświadczony wykonawca zaproponuje na tej podstawie rozwiązania, o których sam byś nie pomyślał.</p>

<h3>2. Kto będzie z tego korzystał</h3>
<p>Klient indywidualny szukający hydraulika przez telefon i dyrektor działu zakupów porównujący dostawców na komputerze to dwa różne projekty. Jedno zdanie o odbiorcy zmienia decyzje projektowe bardziej niż długa lista wymagań.</p>

<h3>3. Zakres w liczbach</h3>
<p>Podaj przybliżoną liczbę podstron i zaznacz, które wyglądają inaczej niż pozostałe. To najważniejsza informacja dla wyceny - o pracochłonności decydują unikalne układy, nie pozycje w menu.</p>

<h3>4. Stan treści i materiałów</h3>
<p>Napisz wprost, co masz gotowe:</p>
<ul>
<li>teksty - gotowe, do poprawy czy do napisania od zera?</li>
<li>zdjęcia - własne, ze stocka, czy potrzebna sesja?</li>
<li>logo i identyfikacja - istnieją, czy trzeba je stworzyć?</li>
<li>obecna strona - zostaje, migrujemy treści, czy zaczynamy od zera?</li>
</ul>
<p>Brak tej informacji to najczęstsza przyczyna rozjazdu między wyceną a końcową fakturą.</p>

<h3>5. Integracje i wymagania techniczne</h3>
<p>Wymień systemy, z którymi strona ma się łączyć: płatności, system magazynowy, CRM, newsletter, kalendarz rezerwacji. Jeśli firma musi spełniać konkretne wymogi - dostępność WCAG, konkretny hosting, polityka bezpieczeństwa - napisz to od razu, bo potrafi to zmienić wycenę o rząd wielkości.</p>

<h3>6. Budżet i termin</h3>
<p>Podanie widełek nie osłabia Twojej pozycji negocjacyjnej - to najczęstszy mit przy zlecaniu projektów. Budżet działa jak filtr: wykonawca albo dopasuje zakres do Twoich możliwości, albo od razu powie, że przy tej kwocie się nie podejmie. Oszczędzacie sobie nawzajem tygodnia korespondencji.</p>

<p>Przy terminie zaznacz, czy data jest sztywna (np. targi, start kampanii), czy orientacyjna. Sztywny termin to realny koszt po stronie wykonawcy i lepiej, żeby wiedział o nim od początku.</p>

<h2>Szablon do skopiowania</h2>

<blockquote>
<p><strong>Czym się zajmujemy:</strong> [branża, wielkość firmy, obszar działania]</p>
<p><strong>Cel strony:</strong> [co ma się dziać po uruchomieniu]</p>
<p><strong>Odbiorcy:</strong> [kto wchodzi na stronę i czego szuka]</p>
<p><strong>Zakres:</strong> około [liczba] podstron, w tym [które wyglądają inaczej]</p>
<p><strong>Treści:</strong> [gotowe / do przeniesienia / do napisania]</p>
<p><strong>Materiały:</strong> [logo, zdjęcia, identyfikacja - co jest, czego nie ma]</p>
<p><strong>Integracje:</strong> [płatności, CRM, newsletter, inne systemy]</p>
<p><strong>Inspiracje:</strong> [2-3 linki i jedno zdanie, co konkretnie się podoba]</p>
<p><strong>Budżet:</strong> [widełki]</p>
<p><strong>Termin:</strong> [data + czy sztywna]</p>
<p><strong>Po wdrożeniu:</strong> [czy potrzebna opieka techniczna i szkolenie z obsługi]</p>
</blockquote>

<h2>Cztery błędy, które psują wyceny</h2>

<ul>
<li><strong>Lista technologii zamiast opisu potrzeby.</strong> „Ma być w React i Node" bez uzasadnienia zawęża grono wykonawców i często podnosi cenę bez korzyści dla Ciebie. Opisz problem, a wybór narzędzi zostaw specjaliście.</li>
<li><strong>„Coś prostego, jak u konkurencji".</strong> Strona konkurencji może mieć za sobą pół roku pracy zespołu. Odsyłacz jest przydatny, ale tylko z informacją, co dokładnie ma być podobne.</li>
<li><strong>Ukrywanie budżetu do końca rozmowy.</strong> Kończy się serią ofert nietrafionych w obie strony.</li>
<li><strong>Zbieranie ofert bez decyzji o zakresie.</strong> Jeśli w trakcie rozmów zakres rośnie, wyceny przestają być porównywalne, a wykonawcy tracą zaufanie do zlecenia.</li>
</ul>

<h2>Co dalej</h2>

<p>Gdy masz już opis, ostatnia rzecz przed wysłaniem: przeczytaj go, wyobrażając sobie, że nie znasz swojej firmy. Jeśli w którymś miejscu musiałbyś dopytać - dopisz to zdanie teraz, zamiast odpowiadać na to samo pytanie pięć razy w wiadomościach.</p>

<p>Orientacyjne widełki cenowe dla różnych typów projektów zebraliśmy w osobnym tekście: <a href="/blog/ile-kosztuje-strona-internetowa-2026">ile kosztuje strona internetowa</a>. A gdy opis jest gotowy - <a href="/announcements/create">opublikuj zlecenie</a> i poczekaj na oferty.</p>
HTML;
    }
}
