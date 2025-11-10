<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Regulamin Serwisu</h1>
            <p class="text-gray-600">
                <strong>Data ostatniej aktualizacji:</strong> {{ now()->format('d.m.Y') }}<br>
                <strong>Obowiązuje od:</strong> 01.01.2024
            </p>
        </div>

        {{-- Content --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 prose prose-blue max-w-none">

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">1. Postanowienia ogólne</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">1.1. Definicje</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li><strong>Serwis</strong> - platforma WebFreelance dostępna pod adresem webfreelance.pl</li>
                <li><strong>Administrator</strong> - WebFreelance Sp. z o.o., ul. Przykładowa 123, 00-001 Warszawa</li>
                <li><strong>Użytkownik</strong> - każda osoba korzystająca z Serwisu</li>
                <li><strong>Konto</strong> - zarejestrowane konto użytkownika w Serwisie</li>
                <li><strong>Ogłoszenie</strong> - treść publikowana przez Użytkownika w Serwisie</li>
                <li><strong>Klient</strong> - Użytkownik poszukujący freelancera</li>
                <li><strong>Freelancer</strong> - Użytkownik świadczący usługi</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">1.2. Zakres regulaminu</h3>
            <p class="text-gray-700 mb-4">
                Niniejszy Regulamin określa zasady korzystania z Serwisu WebFreelance, w tym prawa i obowiązki Użytkowników oraz Administratora.
            </p>
            <p class="text-gray-700 mb-4">
                Korzystanie z Serwisu jest równoznaczne z akceptacją Regulaminu oraz
                <a href="{{ route('privacy-policy') }}" class="text-blue-600 hover:text-blue-700 underline">Polityki Prywatności</a>.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">2. Rejestracja i konto użytkownika</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2.1. Warunki rejestracji</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Użytkownik musi mieć ukończone 18 lat</li>
                <li>Wymagane jest podanie prawdziwych danych osobowych</li>
                <li>Jeden użytkownik może posiadać jedno konto</li>
                <li>Zakazane jest zakładanie kont dla osób trzecich</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2.2. Weryfikacja konta</h3>
            <p class="text-gray-700 mb-4">
                Administrator zastrzega sobie prawo do weryfikacji danych podanych podczas rejestracji oraz żądania dokumentów potwierdzających tożsamość.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2.3. Bezpieczeństwo konta</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Użytkownik ponosi pełną odpowiedzialność za zachowanie poufności hasła</li>
                <li>Zakazane jest udostępnianie danych logowania osobom trzecim</li>
                <li>O podejrzeniu nieuprawnionego dostępu należy natychmiast poinformować Administratora</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">3. Zasady publikowania ogłoszeń</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.1. Wymagania ogłoszenia</h3>
            <p class="text-gray-700 mb-4">Ogłoszenie musi:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Zawierać prawdziwe i aktualne informacje</li>
                <li>Być opisane w języku polskim</li>
                <li>Dotyczyć legalnych usług/projektów</li>
                <li>Nie zawierać treści sprzecznych z prawem lub dobrymi obyczajami</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.2. Treści zakazane</h3>
            <p class="text-gray-700 mb-4">Zakazane jest publikowanie ogłoszeń:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Propagujących przemoc, rasizm, dyskryminację</li>
                <li>Pornograficznych lub erotycznych</li>
                <li>Naruszających prawa autorskie osób trzecich</li>
                <li>Zawierających spam lub linki afiliacyjne</li>
                <li>Wprowadzających w błąd lub stanowiących oszustwo</li>
                <li>Dotyczących działalności nielegalnej</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3.3. Moderacja</h3>
            <p class="text-gray-700 mb-4">
                Wszystkie ogłoszenia podlegają moderacji przez Administratora przed publikacją.
                Administrator może odrzucić lub usunąć ogłoszenie bez podania przyczyny.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">4. Współpraca między użytkownikami</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.1. Komunikacja</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Użytkownicy komunikują się za pośrednictwem systemu wiadomości Serwisu</li>
                <li>Zakazane jest żądanie kontaktu poza Serwisem przed nawiązaniem współpracy</li>
                <li>Użytkownicy powinni zachować uprzejmość i profesjonalizm</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.2. Umowy i płatności</h3>
            <p class="text-gray-700 mb-4">
                <strong>WAŻNE:</strong> Administrator nie jest stroną umów zawieranych między Klientami a Freelancerami.
                Serwis pełni wyłącznie funkcję platformy pośredniczącej.
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Warunki współpracy ustalane są bezpośrednio między użytkownikami</li>
                <li>Administrator nie ponosi odpowiedzialności za wykonanie umowy</li>
                <li>Administrator nie uczestniczy w transakcjach finansowych między użytkownikami</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4.3. Spory</h3>
            <p class="text-gray-700 mb-4">
                W przypadku sporów między użytkownikami, Administrator może pełnić rolę mediatora,
                ale nie jest zobowiązany do rozstrzygania konfliktów.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">5. Prawa i obowiązki Administratora</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5.1. Administrator ma prawo do:</h3>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Moderacji i usuwania treści naruszających Regulamin</li>
                <li>Zawieszenia lub usunięcia konta użytkownika</li>
                <li>Zmiany funkcjonalności Serwisu</li>
                <li>Tymczasowego wyłączenia Serwisu w celu konserwacji</li>
                <li>Zmiany Regulaminu z 14-dniowym wyprzedzeniem</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5.2. Wyłączenie odpowiedzialności</h3>
            <p class="text-gray-700 mb-4">Administrator nie ponosi odpowiedzialności za:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Treści publikowane przez użytkowników</li>
                <li>Wykonanie lub niewykonanie umów między użytkownikami</li>
                <li>Straty finansowe wynikające ze współpracy między użytkownikami</li>
                <li>Przerwy w działaniu Serwisu (awarie, konserwacja)</li>
                <li>Działania osób trzecich (hackerzy, spam)</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">6. Płatne usługi (przyszłość)</h2>
            <p class="text-gray-700 mb-4">
                Administrator zastrzega sobie prawo do wprowadzenia płatnych funkcji premium w przyszłości, takich jak:
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Wyróżnienie ogłoszenia</li>
                <li>Dostęp do zaawansowanych statystyk</li>
                <li>Weryfikacja konta (badge)</li>
                <li>Priorytetowa widoczność</li>
            </ul>
            <p class="text-gray-700 mb-4">
                Użytkownicy zostaną poinformowani o warunkach płatnych usług przed ich aktywacją.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">7. Reklamy i marketing</h2>
            <p class="text-gray-700 mb-4">
                Serwis wyświetla reklamy dostarczane przez <strong>Google AdSense</strong> oraz może wysyłać newslettery marketingowe.
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Użytkownik może zrezygnować z newslettera w dowolnym momencie</li>
                <li>Reklamy są personalizowane na podstawie zachowań użytkownika (cookies)</li>
                <li>Użytkownik może zarządzać preferencjami reklam w ustawieniach konta</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">8. Własność intelektualna</h2>
            <p class="text-gray-700 mb-4">
                Wszystkie materiały zamieszczone w Serwisie (logo, grafiki, teksty, kod) są własnością Administratora lub licencjonowane.
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Zakazane jest kopiowanie, modyfikowanie lub rozpowszechnianie treści Serwisu bez zgody</li>
                <li>Użytkownik zachowuje prawa autorskie do publikowanych ogłoszeń</li>
                <li>Publikując treści, Użytkownik udziela Administratorowi licencji na wyświetlanie ich w Serwisie</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">9. Usunięcie konta</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">9.1. Usunięcie na żądanie użytkownika</h3>
            <p class="text-gray-700 mb-4">
                Użytkownik może w każdej chwili usunąć swoje konto wysyłając żądanie na adres:
                <a href="mailto:kontakt@webfreelance.pl" class="text-blue-600 hover:text-blue-700 underline">kontakt@webfreelance.pl</a>
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">9.2. Usunięcie przez Administratora</h3>
            <p class="text-gray-700 mb-4">Administrator może usunąć konto w przypadku:</p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Naruszenia Regulaminu</li>
                <li>Publikowania treści zakazanych</li>
                <li>Działań oszukańczych lub spamowych</li>
                <li>Braku aktywności przez okres dłuższy niż 2 lata</li>
            </ul>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">10. Reklamacje</h2>
            <p class="text-gray-700 mb-4">
                Reklamacje dotyczące działania Serwisu należy składać na adres:
                <a href="mailto:reklamacje@webfreelance.pl" class="text-blue-600 hover:text-blue-700 underline">reklamacje@webfreelance.pl</a>
            </p>
            <p class="text-gray-700 mb-4">
                Reklamacja powinna zawierać:
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Dane kontaktowe</li>
                <li>Opis problemu</li>
                <li>Data wystąpienia problemu</li>
                <li>Ewentualne zrzuty ekranu</li>
            </ul>
            <p class="text-gray-700 mb-4">
                <strong>Termin rozpatrzenia:</strong> 14 dni roboczych od otrzymania reklamacji.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">11. Zmiany w Regulaminie</h2>
            <p class="text-gray-700 mb-4">
                Administrator może zmieniać Regulamin z ważnych przyczyn (zmiany prawne, nowe funkcje, itp.).
            </p>
            <p class="text-gray-700 mb-4">
                O zmianach użytkownicy zostaną poinformowani z 14-dniowym wyprzedzeniem przez:
            </p>
            <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                <li>Email na adres przypisany do konta</li>
                <li>Powiadomienie na stronie głównej</li>
            </ul>
            <p class="text-gray-700 mb-4">
                Brak sprzeciwu w ciągu 14 dni oznacza akceptację zmian.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">12. Postanowienia końcowe</h2>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">12.1. Prawo właściwe</h3>
            <p class="text-gray-700 mb-4">
                W sprawach nieuregulowanych Regulaminem zastosowanie mają przepisy prawa polskiego,
                w szczególności Kodeks Cywilny oraz ustawa o świadczeniu usług drogą elektroniczną.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">12.2. Rozstrzyganie sporów</h3>
            <p class="text-gray-700 mb-4">
                Wszelkie spory będą rozstrzygane przez sąd właściwy dla siedziby Administratora (Warszawa).
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">12.3. Klauzula salwatoryjna</h3>
            <p class="text-gray-700 mb-4">
                W przypadku uznania któregokolwiek z postanowień Regulaminu za nieważne, pozostałe postanowienia pozostają w mocy.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">13. Kontakt</h2>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-4">
                <p class="text-gray-700 mb-2"><strong>WebFreelance Sp. z o.o.</strong></p>
                <p class="text-gray-700 mb-2">ul. Przykładowa 123</p>
                <p class="text-gray-700 mb-2">00-001 Warszawa, Polska</p>
                <p class="text-gray-700 mb-2">NIP: 1234567890</p>
                <p class="text-gray-700 mb-2">REGON: 123456789</p>
                <p class="text-gray-700 mb-2">KRS: 0000123456</p>
                <p class="text-gray-700 mb-2">Email: <a href="mailto:kontakt@webfreelance.pl" class="text-blue-600 hover:text-blue-700 underline">kontakt@webfreelance.pl</a></p>
                <p class="text-gray-700 mb-2">Reklamacje: <a href="mailto:reklamacje@webfreelance.pl" class="text-blue-600 hover:text-blue-700 underline">reklamacje@webfreelance.pl</a></p>
                <p class="text-gray-700">Tel: +48 22 123 45 67</p>
            </div>

        </div>

        {{-- Back button --}}
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                ← Powrót do strony głównej
            </a>
        </div>

    </div>
</div>
