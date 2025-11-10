<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Polityka Prywatności</h1>
            <p class="text-gray-600">
                <strong>Data ostatniej aktualizacji:</strong> {{ now()->format('d.m.Y') }}<br>
                <strong>Obowiązuje od:</strong> 01.01.2024
            </p>
        </div>

        {{-- Content --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 prose prose-blue max-w-none">

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">1. Informacje ogólne</h2>
            <p class="text-gray-700 mb-4">
                Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych przekazanych przez Użytkowników w związku z korzystaniem z serwisu <strong>Projekciarz.pl</strong> dostępnego pod adresem <strong>projekciarz.pl</strong>.
            </p>
            <p class="text-gray-700 mb-4">
                Administratorem danych osobowych jest <strong>CleanCode Adrian Sadowski</strong>, NIP: 9880303943.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">2. Rodzaje zbieranych danych</h2>
            <p class="text-gray-700 mb-4">Serwis zbiera następujące kategorie danych:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Dane z rejestracji:</strong> imię i nazwisko, adres email, numer telefonu, nazwa firmy (opcjonalnie)</li>
                <li><strong>Dane analityczne:</strong> adres IP, typ przeglądarki, system operacyjny (Google Analytics)</li>
                <li><strong>Dane dotyczące aktywności:</strong> historia przeglądania, interakcje ze stroną (Google Analytics)</li>
                <li><strong>Pliki cookies:</strong> niezbędne do działania serwisu oraz analityczne (Google Analytics, Google Tag Manager)</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">3. Cel i podstawa prawna przetwarzania danych</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.1. Rejestracja i korzystanie z serwisu</h3>
            <p class="text-gray-700 mb-4">
                <strong>Cel:</strong> Umożliwienie rejestracji konta, logowania, publikowania ogłoszeń<br>
                <strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. b) RODO (wykonanie umowy)
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.2. Analityka i statystyki</h3>
            <p class="text-gray-700 mb-4">
                <strong>Cel:</strong> Analiza ruchu, optymalizacja serwisu, poprawa funkcjonalności (Google Analytics, Google Tag Manager)<br>
                <strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. f) RODO (prawnie uzasadniony interes administratora w optymalizacji serwisu)
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">4. Google Analytics, Google Tag Manager i pliki cookies</h2>
            <p class="text-gray-700 mb-4">
                Serwis wykorzystuje narzędzia Google do analizy ruchu i optymalizacji:
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Google Analytics</strong> - analiza ruchu na stronie, zbieranie statystyk odwiedzin</li>
                <li><strong>Google Tag Manager</strong> - zarządzanie tagami marketingowymi i analitycznymi</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.1. Rodzaje cookies:</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Cookies niezbędne:</strong> umożliwiają podstawowe funkcje (logowanie, sesja, bezpieczeństwo)</li>
                <li><strong>Cookies analityczne:</strong> Google Analytics, Google Tag Manager (zbieranie statystyk, optymalizacja serwisu)</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.2. Zarządzanie cookies:</h3>
            <p class="text-gray-700 mb-4">
                Użytkownik może w każdej chwili zmienić ustawienia cookies w swojej przeglądarce. Wyłączenie cookies analitycznych nie wpływa na podstawową funkcjonalność serwisu, ale może ograniczyć możliwość optymalizacji doświadczenia użytkownika.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">5. Hosting i bezpieczeństwo danych</h2>
            <p class="text-gray-700 mb-4">
                Serwis jest hostowany na serwerach <strong>OVH</strong> (OVH SAS) zlokalizowanych w Unii Europejskiej (Francja).
                OVH jest renomowanym dostawcą usług hostingowych stosującym najwyższe standardy bezpieczeństwa i ochrony danych.
            </p>
            <p class="text-gray-700 mb-4">
                <strong>Środki bezpieczeństwa:</strong>
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Szyfrowanie połączenia SSL/TLS</li>
                <li>Szyfrowanie haseł (bcrypt)</li>
                <li>Regularne kopie zapasowe</li>
                <li>Ochrona przed atakami DDoS</li>
                <li>Zapory ogniowe (firewall)</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">6. Udostępnianie danych osobom trzecim</h2>
            <p class="text-gray-700 mb-4">Dane osobowe mogą być przekazywane następującym odbiorcom:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>OVH SAS</strong> - hosting serwisu (serwery w UE)</li>
                <li><strong>Google LLC</strong> - Google Analytics, Google Tag Manager (analityka)</li>
                <li><strong>Dostawcy poczty email</strong> - wysyłka powiadomień systemowych</li>
                <li><strong>Organy państwowe</strong> - na żądanie uprawnione zgodnie z prawem</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">7. Prawa użytkownika (RODO)</h2>
            <p class="text-gray-700 mb-4">Użytkownikowi przysługują następujące prawa:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Prawo dostępu</strong> - do swoich danych osobowych</li>
                <li><strong>Prawo do sprostowania</strong> - poprawiania nieprawidłowych danych</li>
                <li><strong>Prawo do usunięcia</strong> - "prawo do bycia zapomnianym"</li>
                <li><strong>Prawo do ograniczenia przetwarzania</strong> - w określonych sytuacjach</li>
                <li><strong>Prawo do przenoszenia danych</strong> - otrzymanie danych w formacie strukturalnym</li>
                <li><strong>Prawo do sprzeciwu</strong> - wobec przetwarzania danych</li>
                <li><strong>Prawo do cofnięcia zgody</strong> - w dowolnym momencie</li>
            </ul>
            <p class="text-gray-700 mb-4">
                <strong>Aby skorzystać z praw, skontaktuj się:</strong> <a href="mailto:biuro@cleancodeas.pl" class="text-blue-600 hover:text-blue-700 underline">biuro@cleancodeas.pl</a>
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">8. Okres przechowywania danych</h2>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Dane konta:</strong> do momentu usunięcia konta lub żądania usunięcia danych</li>
                <li><strong>Dane analityczne Google Analytics:</strong> 26 miesięcy od ostatniej aktywności</li>
                <li><strong>Logi systemowe:</strong> 90 dni</li>
                <li><strong>Korespondencja email:</strong> do momentu zakończenia sprawy</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">9. Zmiany w Polityce Prywatności</h2>
            <p class="text-gray-700 mb-4">
                Administrator zastrzega sobie prawo do wprowadzania zmian w niniejszej Polityce Prywatności.
                O wszelkich zmianach użytkownicy zostaną poinformowani za pośrednictwem wiadomości email lub powiadomienia na stronie.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">10. Kontakt</h2>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-4">
                <p class="text-gray-700 mb-2"><strong>CleanCode Adrian Sadowski</strong></p>
                <p class="text-gray-700 mb-2">NIP: 9880303943</p>
                <p class="text-gray-700 mb-2">Email: <a href="mailto:biuro@cleancodeas.pl" class="text-blue-600 hover:text-blue-700 underline">biuro@cleancodeas.pl</a></p>
                <p class="text-gray-700">Email (sprawy RODO): <a href="mailto:biuro@cleancodeas.pl" class="text-blue-600 hover:text-blue-700 underline">biuro@cleancodeas.pl</a></p>
            </div>

            <p class="text-gray-600 text-sm mt-8 italic">
                Masz prawo wniesienia skargi do organu nadzorczego (UODO - Urząd Ochrony Danych Osobowych)
                w przypadku uznania, że przetwarzanie Twoich danych osobowych narusza przepisy RODO.
            </p>

        </div>

        {{-- Back button --}}
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                ← Powrót do strony głównej
            </a>
        </div>

    </div>
</div>
