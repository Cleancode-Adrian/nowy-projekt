<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Polityka prywatności
        Page::updateOrCreate(
            ['slug' => 'polityka-prywatnosci'],
            [
                'title' => 'Polityka Prywatności',
                'content' => '<h2>1. Informacje ogólne</h2>
<p>Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych przekazanych przez Użytkowników w związku z korzystaniem z serwisu <strong>Projekciarz.pl</strong> dostępnego pod adresem <strong>projekciarz.pl</strong>.</p>
<p>Administratorem danych osobowych jest <strong>CleanCode Adrian Sadowski</strong>, NIP: 9880303943.</p>

<h2>2. Rodzaje zbieranych danych</h2>
<p>Serwis zbiera następujące kategorie danych:</p>
<ul>
    <li><strong>Dane z rejestracji:</strong> imię i nazwisko, adres email, numer telefonu, nazwa firmy (opcjonalnie)</li>
    <li><strong>Dane analityczne:</strong> adres IP, typ przeglądarki, system operacyjny (Google Analytics)</li>
    <li><strong>Dane dotyczące aktywności:</strong> historia przeglądania, interakcje ze stroną (Google Analytics)</li>
    <li><strong>Pliki cookies:</strong> niezbędne do działania serwisu oraz analityczne (Google Analytics, Google Tag Manager)</li>
</ul>

<h2>3. Cel i podstawa prawna przetwarzania danych</h2>
<h3>3.1. Rejestracja i korzystanie z serwisu</h3>
<p><strong>Cel:</strong> Umożliwienie rejestracji konta, logowania, publikowania ogłoszeń<br>
<strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. b) RODO (wykonanie umowy)</p>

<h3>3.2. Analityka i statystyki</h3>
<p><strong>Cel:</strong> Analiza ruchu, optymalizacja serwisu, poprawa funkcjonalności (Google Analytics, Google Tag Manager)<br>
<strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. f) RODO (prawnie uzasadniony interes administratora w optymalizacji serwisu)</p>

<h2>4. Google Analytics, Google Tag Manager i pliki cookies</h2>
<p>Serwis wykorzystuje narzędzia Google do analizy ruchu i optymalizacji:</p>
<ul>
    <li><strong>Google Analytics</strong> - analiza ruchu na stronie, zbieranie statystyk odwiedzin</li>
    <li><strong>Google Tag Manager</strong> - zarządzanie tagami marketingowymi i analitycznymi</li>
</ul>

<h2>5. Hosting i bezpieczeństwo danych</h2>
<p>Serwis jest hostowany na serwerach <strong>OVH</strong> (OVH SAS) zlokalizowanych w Unii Europejskiej (Francja). OVH jest renomowanym dostawcą usług hostingowych stosującym najwyższe standardy bezpieczeństwa i ochrony danych.</p>

<h2>6. Prawa użytkownika (RODO)</h2>
<p>Użytkownikowi przysługują następujące prawa:</p>
<ul>
    <li><strong>Prawo dostępu</strong> - do swoich danych osobowych</li>
    <li><strong>Prawo do sprostowania</strong> - poprawiania nieprawidłowych danych</li>
    <li><strong>Prawo do usunięcia</strong> - "prawo do bycia zapomnianym"</li>
    <li><strong>Prawo do ograniczenia przetwarzania</strong> - w określonych sytuacjach</li>
    <li><strong>Prawo do przenoszenia danych</strong> - otrzymanie danych w formacie strukturalnym</li>
    <li><strong>Prawo do sprzeciwu</strong> - wobec przetwarzania danych</li>
    <li><strong>Prawo do cofnięcia zgody</strong> - w dowolnym momencie</li>
</ul>
<p><strong>Aby skorzystać z praw, skontaktuj się:</strong> <a href="mailto:biuro@cleancodeas.pl">biuro@cleancodeas.pl</a></p>

<h2>7. Kontakt</h2>
<p><strong>CleanCode Adrian Sadowski</strong><br>
NIP: 9880303943<br>
Email: <a href="mailto:biuro@cleancodeas.pl">biuro@cleancodeas.pl</a></p>',
                'meta_title' => 'Polityka prywatności - Projekciarz.pl',
                'meta_description' => 'Polityka prywatności platformy Projekciarz.pl. Dowiedz się, jak chronimy Twoje dane osobowe zgodnie z RODO.',
                'is_active' => true,
                'is_system' => true,
                'order' => 1,
                'show_in_menu' => true,
                'menu_position' => 'footer',
                'menu_order' => 2,
            ]
        );

        // Regulamin
        Page::updateOrCreate(
            ['slug' => 'regulamin'],
            [
                'title' => 'Regulamin Serwisu',
                'content' => '<h2>1. Postanowienia ogólne</h2>
<h3>1.1. Definicje</h3>
<ul>
    <li><strong>Serwis</strong> - platforma Projekciarz.pl dostępna pod adresem projekciarz.pl</li>
    <li><strong>Administrator</strong> - CleanCode Adrian Sadowski, NIP: 9880303943</li>
    <li><strong>Użytkownik</strong> - każda osoba korzystająca z Serwisu</li>
    <li><strong>Konto</strong> - zarejestrowane konto użytkownika w Serwisie</li>
    <li><strong>Ogłoszenie</strong> - treść publikowana przez Użytkownika w Serwisie</li>
    <li><strong>Klient</strong> - Użytkownik poszukujący freelancera</li>
    <li><strong>Freelancer</strong> - Użytkownik świadczący usługi</li>
</ul>

<h3>1.2. Zakres regulaminu</h3>
<p>Niniejszy Regulamin określa zasady korzystania z Serwisu Projekciarz.pl, w tym prawa i obowiązki Użytkowników oraz Administratora.</p>
<p>Korzystanie z Serwisu jest równoznaczne z akceptacją Regulaminu oraz <a href="/polityka-prywatnosci">Polityki Prywatności</a>.</p>

<h2>2. Rejestracja i konto użytkownika</h2>
<h3>2.1. Warunki rejestracji</h3>
<ul>
    <li>Użytkownik musi mieć ukończone 18 lat</li>
    <li>Wymagane jest podanie prawdziwych danych osobowych</li>
    <li>Jeden użytkownik może posiadać jedno konto</li>
    <li>Zakazane jest zakładanie kont dla osób trzecich</li>
</ul>

<h3>2.2. Weryfikacja konta</h3>
<p>Administrator zastrzega sobie prawo do weryfikacji danych podanych podczas rejestracji oraz żądania dokumentów potwierdzających tożsamość.</p>

<h2>3. Publikowanie ogłoszeń</h2>
<p>Użytkownicy mogą publikować ogłoszenia zgodnie z zasadami określonymi w Serwisie. Administrator zastrzega sobie prawo do moderacji i usuwania ogłoszeń naruszających Regulamin.</p>

<h2>4. Odpowiedzialność</h2>
<p>Użytkownik ponosi pełną odpowiedzialność za treści publikowane w Serwisie. Administrator nie ponosi odpowiedzialności za treści dodane przez Użytkowników.</p>

<h2>5. Kontakt</h2>
<p><strong>CleanCode Adrian Sadowski</strong><br>
NIP: 9880303943<br>
Email: <a href="mailto:biuro@cleancodeas.pl">biuro@cleancodeas.pl</a></p>',
                'meta_title' => 'Regulamin serwisu - Projekciarz.pl',
                'meta_description' => 'Regulamin korzystania z platformy Projekciarz.pl. Zapoznaj się z zasadami korzystania z serwisu.',
                'is_active' => true,
                'is_system' => true,
                'order' => 2,
                'show_in_menu' => true,
                'menu_position' => 'footer',
                'menu_order' => 1,
            ]
        );
    }
}

