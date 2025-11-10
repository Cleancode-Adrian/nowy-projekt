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
                <li><strong>Dane identyfikacyjne:</strong> imię, nazwisko, adres email</li>
                <li><strong>Dane kontaktowe:</strong> numer telefonu, nazwa firmy</li>
                <li><strong>Dane techniczne:</strong> adres IP, typ przeglądarki, system operacyjny</li>
                <li><strong>Dane dotyczące aktywności:</strong> historia przeglądania, kliknięcia, czas spędzony na stronie</li>
                <li><strong>Pliki cookies:</strong> pliki przechowywane w urządzeniu użytkownika</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">3. Cel i podstawa prawna przetwarzania danych</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.1. Rejestracja i korzystanie z serwisu</h3>
            <p class="text-gray-700 mb-4">
                <strong>Cel:</strong> Umożliwienie rejestracji konta, logowania, publikowania ogłoszeń<br>
                <strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. b) RODO (wykonanie umowy)
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.2. Marketing i reklamy</h3>
            <p class="text-gray-700 mb-4">
                <strong>Cel:</strong> Wyświetlanie spersonalizowanych reklam, analiza zachowań użytkowników<br>
                <strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. a) RODO (zgoda) oraz Art. 6 ust. 1 lit. f) RODO (prawnie uzasadniony interes)
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.3. Analityka i statystyki</h3>
            <p class="text-gray-700 mb-4">
                <strong>Cel:</strong> Analiza ruchu, optymalizacja serwisu, poprawa funkcjonalności<br>
                <strong>Podstawa prawna:</strong> Art. 6 ust. 1 lit. f) RODO (prawnie uzasadniony interes)
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">4. Google Analytics i pliki cookies</h2>
            <p class="text-gray-700 mb-4">
                Serwis wykorzystuje <strong>Google Analytics</strong> - narzędzie do analizy ruchu na stronie, które zbiera informacje za pomocą plików cookies.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.1. Rodzaje cookies:</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Cookies niezbędne:</strong> umożliwiają podstawowe funkcje (logowanie, sesja)</li>
                <li><strong>Cookies analityczne:</strong> Google Analytics (zbieranie statystyk)</li>
                <li><strong>Cookies marketingowe:</strong> Google AdSense, Facebook Pixel (personalizacja reklam)</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.2. Zarządzanie cookies:</h3>
            <p class="text-gray-700 mb-4">
                Użytkownik może w każdej chwili zmienić ustawienia cookies w swojej przeglądarce. Wyłączenie cookies może wpłynąć na funkcjonalność serwisu.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">5. Google AdSense i reklamy</h2>
            <p class="text-gray-700 mb-4">
                Serwis korzysta z <strong>Google AdSense</strong> do wyświetlania reklam. Google może wykorzystywać dane użytkowników do personalizacji reklam zgodnie z własną polityką prywatności.
            </p>
            <p class="text-gray-700 mb-4">
                <strong>Możesz zrezygnować z personalizowanych reklam:</strong>
                <a href="https://adssettings.google.com" target="_blank" class="text-blue-600 hover:text-blue-700 underline">Ustawienia reklam Google</a>
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">6. Hosting i bezpieczeństwo danych</h2>
            <p class="text-gray-700 mb-4">
                Serwis jest hostowany na serwerach VPS zlokalizowanych w Unii Europejskiej.
                Stosujemy odpowiednie środki techniczne i organizacyjne zapewniające bezpieczeństwo danych.
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

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">7. Udostępnianie danych osobom trzecim</h2>
            <p class="text-gray-700 mb-4">Dane osobowe mogą być przekazywane następującym odbiorcom:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Dostawca hostingu VPS</strong> - hosting serwisu</li>
                <li><strong>Dostawcy poczty email</strong> - wysyłka powiadomień</li>
                <li><strong>Organy państwowe</strong> - na żądanie uprawnione zgodnie z prawem</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">8. Prawa użytkownika (RODO)</h2>
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

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">9. Okres przechowywania danych</h2>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Dane konta:</strong> do momentu usunięcia konta lub 3 lata od ostatniego logowania</li>
                <li><strong>Dane analityczne:</strong> 26 miesięcy (Google Analytics)</li>
                <li><strong>Logi systemowe:</strong> 90 dni</li>
                <li><strong>Korespondencja:</strong> 3 lata od zakończenia sprawy</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">10. Zmiany w Polityce Prywatności</h2>
            <p class="text-gray-700 mb-4">
                Administrator zastrzega sobie prawo do wprowadzania zmian w niniejszej Polityce Prywatności.
                O wszelkich zmianach użytkownicy zostaną poinformowani za pośrednictwem wiadomości email lub powiadomienia na stronie.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">11. Kontakt</h2>
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
