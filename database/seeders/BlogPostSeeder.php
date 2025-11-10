<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->warn('Brak użytkownika admin - pomijam seeder blogów');
            return;
        }

        // Tagi do przypisania
        $phpTag = Tag::where('name', 'PHP')->first();
        $javascriptTag = Tag::where('name', 'JavaScript')->first();
        $designTag = Tag::where('name', 'Projektowanie')->first();

        // Artykuł 1: Jak rozpocząć karierę freelancera
        $post1 = BlogPost::create([
            'author_id' => $admin->id,
            'title' => 'Jak rozpocząć karierę freelancera w 2025 roku?',
            'slug' => 'jak-rozpoczac-kariere-freelancera-2025',
            'excerpt' => 'Kompletny przewodnik dla osób, które chcą rozpocząć pracę jako freelancer. Dowiedz się, jak znaleźć pierwszych klientów, ustalić stawki i zbudować portfolio.',
            'content' => '<h2>Wprowadzenie</h2>
<p>Freelancing to jeden z najbardziej dynamicznie rozwijających się modeli pracy w 2025 roku. Coraz więcej osób decyduje się na niezależność zawodową, elastyczne godziny pracy i możliwość wyboru projektów. W tym artykule pokażemy Ci, jak rozpocząć karierę freelancera krok po kroku.</p>

<h2>1. Określ swoją specjalizację</h2>
<p>Pierwszym krokiem jest zdefiniowanie, w czym jesteś dobry i co chcesz oferować klientom. Najpopularniejsze obszary to:</p>
<ul>
    <li><strong>Programowanie</strong> - tworzenie stron internetowych, aplikacji mobilnych, systemów backendowych</li>
    <li><strong>Projektowanie graficzne</strong> - logo, branding, materiały marketingowe</li>
    <li><strong>Marketing internetowy</strong> - SEO, content marketing, social media</li>
    <li><strong>Copywriting</strong> - tworzenie treści, tekstów sprzedażowych, blogów</li>
    <li><strong>Tłumaczenia</strong> - przekłady dokumentów, lokalizacja aplikacji</li>
</ul>

<h2>2. Zbuduj portfolio</h2>
<p>Portfolio to Twoja wizytówka w świecie freelancingu. Nawet jeśli dopiero zaczynasz, możesz stworzyć przykładowe projekty lub pracować pro bono dla organizacji non-profit.</p>
<p><strong>Wskazówki:</strong></p>
<ul>
    <li>Pokaż 3-5 najlepszych projektów zamiast wszystkiego co zrobiłeś</li>
    <li>Opisz problem, rozwiązanie i rezultaty każdego projektu</li>
    <li>Dodaj case study z wynikami (np. "wzrost konwersji o 45%")</li>
    <li>Umieść portfolio na własnej stronie internetowej</li>
</ul>

<h2>3. Ustal konkurencyjne stawki</h2>
<p>Ustalanie stawek to jeden z najtrudniejszych aspektów freelancingu. Na początku możesz:</p>
<ul>
    <li>Sprawdzić stawki konkurencji na platformach takich jak WebFreelance</li>
    <li>Zacząć niżej, aby zdobyć pierwsze referencje</li>
    <li>Stopniowo podnosić ceny wraz z doświadczeniem</li>
</ul>

<h2>4. Zarejestruj się na platformach freelancerskich</h2>
<p>Platformy takie jak <strong>WebFreelance</strong> to najlepsze miejsce na znalezienie pierwszych klientów. Pamiętaj o:</p>
<ul>
    <li>Wypełnieniu profilu w 100%</li>
    <li>Dodaniu profesjonalnego zdjęcia</li>
    <li>Dokładnym opisaniu swoich umiejętności</li>
    <li>Regularnym aktualizowaniu portfolio</li>
</ul>

<h2>5. Składaj przemyślane oferty</h2>
<p>Nie wysyłaj generycznych wiadomości. Każda oferta powinna być spersonalizowana:</p>
<ul>
    <li>Przeczytaj dokładnie ogłoszenie</li>
    <li>Odnieś się do konkretnych wymagań klienta</li>
    <li>Pokaż, jak rozwiązałeś podobne problemy w przeszłości</li>
    <li>Zaproponuj wartość dodaną</li>
</ul>

<h2>6. Buduj długotrwałe relacje</h2>
<p>Najlepsi klienci to ci, którzy wracają. Dbaj o:</p>
<ul>
    <li>Terminowość i jakość</li>
    <li>Regularną komunikację</li>
    <li>Proaktywne podejście (sugeruj ulepszenia)</li>
    <li>Profesjonalizm w każdej sytuacji</li>
</ul>

<h2>Podsumowanie</h2>
<p>Rozpoczęcie kariery freelancera wymaga planowania, ale może być niezwykle satysfakcjonujące. Najważniejsze to zacząć, zdobywać doświadczenie i nieustannie się rozwijać. Platforma <strong>WebFreelance</strong> jest idealnym miejscem na pierwsze kroki w świecie freelancingu!</p>

<p><strong>Gotowy na start? <a href="/rejestracja">Załóż bezpłatne konto</a> i zacznij zarabiać już dziś!</strong></p>',
            'meta_title' => 'Jak Zostać Freelancerem w 2025? Kompletny Przewodnik Krok po Kroku',
            'meta_description' => 'Dowiedz się, jak rozpocząć karierę freelancera w 2025. Ustalanie stawek, budowanie portfolio, znajdowanie klientów i więcej. Praktyczny przewodnik dla początkujących.',
            'meta_keywords' => ['freelancer', 'praca zdalna', 'kariera freelancera', 'jak zostać freelancerem', 'freelancing 2025', 'portfolio freelancera', 'stawki freelancera'],
            'status' => 'published',
            'published_at' => now()->subDays(7),
        ]);

        if ($phpTag) $post1->tags()->attach($phpTag);
        if ($designTag) $post1->tags()->attach($designTag);

        // Artykuł 2: Najlepsze praktyki w komunikacji z klientem
        $post2 = BlogPost::create([
            'author_id' => $admin->id,
            'title' => '10 zasad skutecznej komunikacji z klientem jako freelancer',
            'slug' => '10-zasad-komunikacji-z-klientem-freelancer',
            'excerpt' => 'Dobra komunikacja to klucz do sukcesu w freelancingu. Poznaj 10 sprawdzonych zasad, które pomogą Ci budować trwałe relacje z klientami i unikać nieporozumień.',
            'content' => '<h2>Dlaczego komunikacja jest tak ważna?</h2>
<p>W świecie freelancingu umiejętność komunikacji jest równie ważna jak kompetencje techniczne. Dobra komunikacja buduje zaufanie, zapobiega konfliktom i prowadzi do długoterminowej współpracy. Oto 10 zasad, które zmienią sposób, w jaki rozmawiasz z klientami.</p>

<h2>1. Odpowiadaj szybko</h2>
<p>Czas reakcji ma ogromne znaczenie. Staraj się odpowiadać na wiadomości w ciągu <strong>24 godzin</strong>, najlepiej w ciągu kilku godzin. Nawet jeśli nie masz jeszcze pełnej odpowiedzi, potwierdź odbiór wiadomości.</p>
<p><em>Przykład: "Dziękuję za wiadomość! Przeanalizuję to dokładnie i wrócę do Ciebie jutro z propozycją."</em></p>

<h2>2. Ustal jasne zasady współpracy</h2>
<p>Przed rozpoczęciem projektu określ:</p>
<ul>
    <li>Zakres prac (co jest, a co nie jest wliczone)</li>
    <li>Terminy realizacji</li>
    <li>Sposób i częstotliwość komunikacji</li>
    <li>Warunki płatności</li>
    <li>Liczbę darmowych poprawek</li>
</ul>

<h2>3. Dokumentuj wszystko na piśmie</h2>
<p>Każdą ważną decyzję, zmianę w projekcie lub ustalenie potwierdź e-mailem lub wiadomością. To zabezpieczenie dla obu stron.</p>
<p><strong>Wskazówka:</strong> Po rozmowie telefonicznej wyślij podsumowanie: "Dziękuję za rozmowę. Ustaliśmy, że..."</p>

<h2>4. Używaj zrozumiałego języka</h2>
<p>Nie każdy klient zna branżowy żargon. Tłumacz techniczne zagadnienia prostym językiem:</p>
<ul>
    <li>❌ "Zoptymalizujemy SEO przez implementację structured data i canonical tags"</li>
    <li>✅ "Poprawimy widoczność w Google, dzięki czemu więcej osób znajdzie Twoją stronę"</li>
</ul>

<h2>5. Regularnie informuj o postępach</h2>
<p>Nie czekaj, aż klient zapyta o status projektu. Wysyłaj regularne aktualizacje:</p>
<ul>
    <li>Co udało się zrobić od ostatniej aktualizacji</li>
    <li>Nad czym obecnie pracujesz</li>
    <li>Czy są jakieś przeszkody</li>
    <li>Czy projekt jest na dobrej drodze terminowej</li>
</ul>

<h2>6. Bądź proaktywny</h2>
<p>Jeśli widzisz potencjalny problem lub możliwość ulepszenia, powiedz o tym:</p>
<p><em>"Zauważyłem, że dodanie tej funkcji może zwiększyć konwersję. Chcesz, abym przygotował wycenę?"</em></p>

<h2>7. Zarządzaj oczekiwaniami</h2>
<p>Lepiej obiecać mniej i dostarczyć więcej, niż odwrotnie. Jeśli nie jesteś pewien terminu, daj sobie bufor:</p>
<ul>
    <li>❌ "Zrobię to w 3 dni"</li>
    <li>✅ "Potrzebuję 5 dni, ale postaram się skończyć wcześniej"</li>
</ul>

<h2>8. Naucz się mówić "nie"</h2>
<p>Czasem klient prosi o coś, co wykracza poza zakres lub Twoje kompetencje. W takiej sytuacji:</p>
<ul>
    <li>Wyjaśnij, dlaczego to nie jest dobry pomysł</li>
    <li>Zaproponuj alternatywę</li>
    <li>Lub przekieruj do specjalisty w tej dziedzinie</li>
</ul>

<h2>9. Zbieraj feedback</h2>
<p>Po zakończeniu projektu zapytaj o opinię:</p>
<p><em>"Jak oceniasz naszą współpracę? Co mogłoby być lepsze?"</em></p>
<p>Konstruktywna krytyka pomoże Ci się rozwijać, a klient doceni, że Ci zależy.</p>

<h2>10. Zakończ projekty profesjonalnie</h2>
<p>Po dostarczeniu pracy:</p>
<ul>
    <li>Zapytaj, czy wszystko jest w porządku</li>
    <li>Przekaż wszystkie pliki i dostępy</li>
    <li>Poproś o opinię/referencję</li>
    <li>Pozostaw otwarte drzwi do przyszłej współpracy</li>
</ul>

<h2>Bonus: Narzędzia ułatwiające komunikację</h2>
<p>Warto korzystać z profesjonalnych narzędzi:</p>
<ul>
    <li><strong>Slack/Discord</strong> - szybka komunikacja</li>
    <li><strong>Trello/Asana</strong> - zarządzanie zadaniami</li>
    <li><strong>Loom</strong> - nagrania wideo z ekranu</li>
    <li><strong>Google Meet/Zoom</strong> - videokonferencje</li>
    <li><strong>WebFreelance</strong> - centralna platforma do komunikacji i płatności</li>
</ul>

<h2>Podsumowanie</h2>
<p>Skuteczna komunikacja to fundament udanego freelancingu. Pamiętaj o tych 10 zasadach, a zobaczysz, jak poprawią się Twoje relacje z klientami i wzrośnie liczba powtarzających się zleceń.</p>

<p><strong>Szukasz projektów, gdzie możesz wykorzystać te umiejętności? <a href="/ogloszenia">Przeglądaj aktualne ogłoszenia</a> na WebFreelance!</strong></p>',
            'meta_title' => 'Komunikacja z Klientem: 10 Zasad dla Freelancera',
            'meta_description' => 'Skuteczna komunikacja to klucz do sukcesu freelancera. Sprawdź 10 zasad, które pomogą Ci budować trwałe relacje z klientami i unikać konfliktów.',
            'meta_keywords' => ['komunikacja z klientem', 'freelancer', 'relacje z klientem', 'zarządzanie projektem', 'współpraca z klientem', 'profesjonalizm', 'freelancing'],
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        if ($javascriptTag) $post2->tags()->attach($javascriptTag);
        if ($designTag) $post2->tags()->attach($designTag);

        $this->command->info('✅ Dodano 2 artykuły blogowe z pełnymi danymi SEO!');
    }
}

