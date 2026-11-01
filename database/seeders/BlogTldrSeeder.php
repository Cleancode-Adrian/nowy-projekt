<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

/**
 * TL;DR dla wpisów z seederów. Idempotentny - nadpisuje tylko tldr, nic więcej.
 * php artisan db:seed --class=BlogTldrSeeder
 */
class BlogTldrSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->tldr() as $slug => $lines) {
            BlogPost::where('slug', $slug)->update(['tldr' => implode("\n", $lines)]);
        }
    }

    /**
     * slug => punkty TL;DR. Punkty to czyste zdania bez prefiksu "- " - widok
     * renderuje je jako <ul>, a w kolumnie lądują rozdzielone znakiem nowej linii.
     *
     * @return array<string, list<string>>
     */
    private function tldr(): array
    {
        return [
            // BlogPostSeeder
            'jak-rozpoczac-kariere-freelancera-2025' => [
                'Zacznij od wyboru specjalizacji: programowanie, grafika, marketing, copywriting lub tłumaczenia',
                'Portfolio: 3–5 najlepszych projektów z opisem problemu, rozwiązania i wyniku, nawet pro bono dla NGO',
                'Na starcie wyceniaj niżej, żeby zdobyć referencje, i podnoś stawki wraz z doświadczeniem',
                'Profil na platformie wypełnij w 100% i personalizuj każdą ofertę zamiast wysyłać szablony',
            ],
            '10-zasad-komunikacji-z-klientem-freelancer' => [
                'Odpowiadaj w ciągu 24 godzin, a jeśli nie masz pełnej odpowiedzi, potwierdź odbiór wiadomości',
                'Przed startem ustal zakres, terminy, warunki płatności i liczbę darmowych poprawek',
                'Każde ustalenie potwierdzaj na piśmie, np. podsumowaniem po rozmowie telefonicznej',
                'Obiecuj mniej, dostarczaj więcej: zamiast „3 dni" powiedz „5 dni, postaram się szybciej"',
            ],
            'portfolio-freelancera-ktore-sprzedaje' => [
                'Portfolio musi mieć hero ze specjalizacją i CTA, case studies, opinie klientów, opis procesu i kontakt',
                'Opisuj projekty przez wyniki i liczby, nie listę funkcji; dodaj wykresy lub cytaty klienta',
                'Pokazuj zrzuty w wersji desktop, tablet i mobile, żeby udowodnić dbałość o responsywność',
                'Każdy projekt kończ krótkim CTA typu „Masz podobne wyzwanie? Porozmawiajmy"',
            ],
            'seo-dla-freelancerow-klienci-z-google' => [
                'Celuj w frazy long-tail opisujące usługę i miasto, np. „projektant UX Kraków"',
                'Regularny blog o problemach klientów buduje widoczność i zaufanie',
                'Darmowa wizytówka Google Moja Firma przynosi lokalne zapytania; uzupełnij profil i zbieraj opinie',
                'Technicznie: szybkie ładowanie (Core Web Vitals), SSL, wersja mobilna i dane strukturalne',
            ],
            'jak-przygotowac-brief-projektowy-freelancer' => [
                'Dobry brief to kontekst biznesowy, precyzyjny zakres (także czego NIE ma) i materiały referencyjne',
                'Określ KPI sukcesu: więcej zapytań z formularza, wyższa konwersja, krótszy proces sprzedaży',
                'Podaj terminy pośrednie, deadline, widełki budżetu i model rozliczenia (ryczałt, godziny, sprinty)',
                'Z góry ustal kanał komunikacji, osoby decyzyjne i maksymalny czas odpowiedzi',
            ],
            'automatyzacja-pracy-freelancera-ai-no-code' => [
                'Baza szablonów odpowiedzi plus ChatGPT/Claude do personalizacji skraca pisanie wiadomości do klientów',
                'Kalkulator wycen w Airtable lub Notion liczy koszt z zakresu, liczby iteracji i progu rentowności',
                'Zapier/Make łączy Trello lub Jirę z Google Docs: zamknięty ticket sam aktualizuje raport dla klienta',
                'Alerty RSS, e-mail i webhook o nowych ogłoszeniach pozwalają składać oferty jako pierwszy',
            ],
            'jak-ustalic-stawki-freelancer-wycena-projektow' => [
                'Stawka godzinowa = miesięczny cel finansowy / płatne godziny; przykład: 12 000 zł / 71,5 h = 168 zł/h',
                'Licz realnie: 20–25 dni roboczych, 4–6 h produktywnej pracy, tylko 60–70% czasu płatne',
                'Ryczałt, gdy zakres jest jasny i szacujesz czas z dokładnością ±20%; godzinowo przy zmiennym zakresie',
                'Zawsze dodaj 20–30% bufora na poprawki i podnoś stawki, gdy kalendarz jest pełny na 2–3 miesiące',
            ],
            'jak-znalezc-pierwszych-klientow-freelancer-strategie' => [
                '7 kanałów: platformy freelancerskie, networking, content marketing, pro bono, cold mailing, agencje, polecenia',
                'Na platformie: profil w 100%, szybkie i spersonalizowane oferty, start od małych zleceń, prośba o opinie',
                'Cold mail: temat z konkretnym problemem firmy, 1–2 zdania rozwiązania, link do portfolio, proste CTA',
                'Plan na 30 dni: 2 tygodnie przygotowań, potem 5–10 ofert dziennie i kontakt z 3–5 firmami',
            ],
            'jak-napisac-skuteczna-oferte-projekciarz' => [
                'Pierwsze 2–3 zdania decydują: odnieś się do konkretnej potrzeby z ogłoszenia, nie „Witam, jestem zainteresowany"',
                'Struktura: otwarcie, doświadczenie z liczbami, podejście, wartość dodana, harmonogram i cena, CTA',
                'Oferta max 200–300 słów, spersonalizowana, bez ogólników typu „doświadczony" i bez błędów językowych',
                'Wartość dodana, np. miesiąc wsparcia, dokumentacja lub szkolenie, wyróżnia ofertę spośród setek innych',
            ],
            'zarzadzanie-czasem-freelancer-produktywnosc' => [
                'Pomodoro: 25 min pracy, 5 min przerwy, po 4 cyklach dłuższa przerwa 15–30 min',
                'Time blocking: najważniejszy projekt rano w godzinach szczytu energii, maile tylko 2–3 razy dziennie',
                'Matryca Eisenhowera: pilne i ważne rób od razu, ważne planuj, pilne nieważne deleguj, resztę usuń',
                'Zadanie poniżej 2 minut zrób natychmiast; stałe godziny pracy i wolne weekendy chronią przed wypaleniem',
            ],
            'podatki-freelancer-polska-2025-jdg-b2b' => [
                'Ryczałt 5,5% dla usług IT jest zwykle najkorzystniejszy przy niskich kosztach; skala to 17% i 32% powyżej 120 tys. zł',
                'Przykład: przy 150 000 zł przychodu ryczałt daje ok. 22 650 zł obciążeń wobec ok. 39 600 zł na skali',
                'Mały ZUS przez pierwsze 24 miesiące ok. 400–500 zł/mies., później ok. 1200–1400 zł/mies.',
                'VAT obowiązkowy po przekroczeniu 200 000 zł przychodu rocznie lub przy usługach dla firm z UE',
            ],
            'freelancing-vs-etat-porownanie-programisci-designerzy' => [
                'Freelancing: 100–300 zł/h i potencjał 2–3x wyższych zarobków, ale miesiące po 30k i po 5k oraz bezpłatny urlop',
                'Etat: 8–20k zł (mid), 20–40k+ (senior), płatne L4, 20–26 dni urlopu i zero administracji',
                'Freelancer poświęca 10–20% czasu na księgowość, marketing, wyceny i umowy',
                'Freelancing ma sens przy 2+ latach doświadczenia i poduszce finansowej na 3–6 miesięcy; możliwa też hybryda',
            ],
            'najlepsze-narzedzia-freelancer-2025' => [
                'Zarządzanie i czas: Notion (od $8/mies.), Trello, Asana, Toggl Track, Clockify (całkowicie darmowy)',
                'Faktury po polsku: Fakturownia 29 zł/mies.; komunikacja: Slack, Discord, Loom; design: Figma, Canva',
                'Automatyzacja: Zapier (100 zadań/mies. za darmo), Make (1000 operacji), n8n (self-hosted za darmo)',
                'Zacznij od darmowych wersji i trzymaj się 5 narzędzi, które znasz, zamiast 20 nieużywanych',
            ],
            'jak-radzic-sobie-trudnymi-klientami-freelancer' => [
                '6 typów trudnych klientów: „wie lepiej", zmienia zdanie, nie płaci, źle komunikuje, nie szanuje czasu, nigdy niezadowolony',
                'Zaliczka 30–50% przed startem, limit darmowych poprawek (np. 2 iteracje), każda zmiana = dodatkowa wycena',
                'Dokumentuj ustalenia e-mailem, ustal jeden kanał komunikacji i godziny pracy, nie odpowiadaj po godzinach',
                'Zakończ współpracę po 30+ dniach braku płatności, przy łamaniu umowy lub gdy szkodzi Twojemu zdrowiu',
            ],
            'case-study-portfolio-freelancera-klienci' => [
                'Start: 3 projekty pro bono dla NGO i 2 zlecenia za 50% stawki w zamian za opinie i case studies',
                'Po 3 latach: odpowiedzi na oferty wzrosły z 5–10% do 30–40%, stawka z 80 do 180 zł/h, 70% klientów z poleceń',
                'Specjalizacja w e-commerce (WooCommerce + konwersja) pozwoliła podnieść stawki o 40%',
                'Każde case study: wyzwanie, proces, mierzalny rezultat, technologie i cytat klienta; lepiej 5 świetnych niż 20 przeciętnych',
            ],

            // AutomationBlogPostsSeeder
            'automatyzacja-procesow-biznesowych-freelancer-2025' => [
                'Case study: automatyzacja fakturowania, komunikacji, raportów i marketingu skróciła 17 h/tydz. do 4 h 45 min',
                'Fakturownia (29 zł/mies.) lub Invoice Ninja: faktury cykliczne i przypomnienia 3 dni przed, w dniu i 3 dni po terminie',
                'Workflow projektu: zapytanie → karta w Trello/Asana → tygodniowe raporty → automatyczna faktura i prośba o opinię',
                'Top narzędzia: Zapier (5000+ integracji), Make, n8n (open source), Power Automate, IFTTT; start od audytu 3 pożeraczy czasu',
            ],
            'narzedzia-ai-dla-freelancerow-2025-chatgpt-claude' => [
                'ChatGPT Plus i Claude Pro po $20/mies., GitHub Copilot $10/mies., Midjourney $10–60, Notion AI $10',
                'Claude radzi sobie z długimi dokumentami (do 200k tokenów), Copilot przyspiesza kodowanie o 55%',
                'Case study: artykuł 2000 słów z 4–5 h do 1,5–2 h, funkcja z 2–3 h do 30–60 min, oszczędność 8–10 h tygodniowo',
                'Pisz konkretne prompty, iteruj, weryfikuj fakty i informuj klientów, gdy używasz AI',
            ],
            'automatyzacja-marketingu-freelancer-2025' => [
                'Sekwencja powitalna: dzień 1 portfolio, dzień 3 case study, dzień 7 opinie, dzień 14 oferta lub darmowa konsultacja',
                'Narzędzia: Buffer od $6/mies., Later $18, Hootsuite $99; outreach: Lemlist, Hunter.io; e-mail: Mailchimp, ConvertKit',
                'Case study po 6 miesiącach: zapytania z 5–8 do 15–20/mies., konwersja z 20% do 35%, przychód +45% (25 000 → 36 250 zł)',
                'Śledź liczbę leadów, konwersję, koszt pozyskania klienta, ROI i zaoszczędzone godziny',
            ],
            'jak-zautomatyzowac-prace-freelancera-praktyczny-przewodnik' => [
                'Krok 1: przez tydzień zapisuj powtarzalne zadania i szereguj je wg częstotliwości, czasu, łatwości i wpływu',
                'Auto-odpowiedź na zapytanie z linkiem do portfolio i formularzem; status projektu wysyłany po zmianie w Trello/Asana',
                'Fakturowanie: faktura po zakończeniu projektu, przypomnienia 3 dni przed, w dniu i 3 dni po, eksport do księgowości',
                'Case study: 15 h/tydz. administracji spadło do 3,5 h; Zapier $20, Make $9, Notion $8, Fakturownia 29 zł/mies.',
            ],

            // BlogPostPricingSeeder
            'ile-kosztuje-strona-internetowa-2026' => [
                'Wizytówka 1 500–4 000 zł, strona firmowa 4 000–12 000 zł, sklep WooCommerce 8 000–30 000 zł, aplikacja MVP 25 000–120 000 zł',
                'Cenę podbijają: liczba unikalnych widoków (nie podstron), projekt od zera (30–50% kosztu), integracje, migracja treści',
                'Wielojęzyczność to +20–40%, pilny termin +20–30%; utrzymanie strony firmowej to 1 500–5 000 zł rocznie',
                'Czerwone flagi: cena bez pytań o zakres, brak zapisu o prawach do kodu i liczbie poprawek, domena na agencję',
            ],

            // BlogPostBriefSeeder
            'jak-opisac-zlecenie-zeby-dostac-dobre-oferty' => [
                'Ogłoszenie „potrzebuję strony dla firmy" dostaje oferty od 2 000 do 25 000 zł, bo każdy wyobraża sobie inny projekt',
                '6 elementów opisu: cel, odbiorcy, zakres w liczbach, stan treści i materiałów, integracje, budżet i termin',
                'Podanie widełek budżetu nie osłabia pozycji; działa jak filtr i oszczędza tygodnia korespondencji',
                'Błędy: lista technologii zamiast potrzeby, „coś prostego jak u konkurencji", ukrywanie budżetu, zmiana zakresu w trakcie',
            ],

            // BlogPostClientGuidesSeeder
            'jak-wybrac-wykonawce-strony-internetowej' => [
                'Freelancer do wizytówek i mniejszych sklepów; agencja przy dużych sklepach, aplikacjach i wielu integracjach',
                'Pytaj o podobne realizacje, proces od briefu do wdrożenia, co zawiera cena i ile rund poprawek jest w cenie',
                'Kod, pliki źródłowe i projekt graficzny po zapłacie muszą być Twoje; domena i hosting zawsze na klienta',
                'Oferty porównuj w tabeli: cena, widoki, grafika, teksty, poprawki, prawa, termin, opieka; najtańsza rzadko nią zostaje',
            ],
            'umowa-z-freelancerem-na-strone-www' => [
                'Na stronę podpisuje się umowę o dzieło; opieka po wdrożeniu to osobna umowa o świadczenie usług',
                'Zakres jako lista podstron, funkcji i integracji plus zdanie, czego umowa nie obejmuje; brief jako załącznik',
                'Etapy z 3–7 dniami na uwagi, 2–3 rundy poprawek z definicją poprawki, transze np. 30/40/30%',
                'Przeniesienie praw autorskich z prawem do modyfikacji, dostępy na klienta, gwarancja 30–90 dni, kara 0,2–0,5%/dzień',
            ],
            'odbior-strony-internetowej-checklista' => [
                '24 punkty w 5 obszarach: wygląd, formularze i funkcje, szybkość i technikalia, SEO, dostępy i formalności',
                'Testuj na prawdziwym telefonie i w drugiej przeglądarce; wyślij każdy formularz i złóż zamówienie testowe',
                'Sprawdź SSL, przekierowanie www/bez www, stronę 404, PageSpeed, zdjęty noindex, sitemap i Search Console na swoje konto',
                'Odbierz komplet dostępów, zmień hasła, poproś o pokaz przywracania kopii; płać po wyzerowaniu listy błędów',
            ],
            'ile-trwa-zrobienie-strony-internetowej' => [
                'Wizytówka 1–3 tygodnie, strona firmowa 4–8 tygodni, sklep 6–12 tygodni, sklep z ERP 3–6 miesięcy, aplikacja 3+ miesiące',
                'To czas kalendarzowy: strona firmowa to 60–80 h pracy rozłożone na 6 tygodni czekania na decyzje i materiały',
                'Etapy: brief 2–5 dni, projekt 1–3 tyg., kodowanie 1–6 tyg., testy 3–10 dni, wdrożenie 1–2 dni',
                'Najbardziej opóźniają: brak treści, decyzje komitetu, zmiany zakresu i integracje bez dostępu do API',
            ],

            // BlogPostTechAndMaintenanceSeeder
            'wordpress-czy-strona-dedykowana' => [
                'Trzy opcje: WordPress (ok. 40% stron świata), kreator (Wix, Webflow, Shopify) i strona dedykowana (Laravel, Next.js)',
                'WordPress: sam edytujesz treści i łatwo zmienisz wykonawcę, ale aktualizacje co kilka tygodni są obowiązkowe',
                'Kreator: zero utrzymania, ale strony nie wyniesiesz poza platformę; dedykowana: pełna kontrola, lecz każda zmiana to programista',
                'Wybór rozstrzygają 4 pytania: kto dodaje treści, jakich funkcji potrzebujesz, kto aktualizuje, co po rozstaniu z wykonawcą',
            ],
            'ile-kosztuje-utrzymanie-strony-internetowej' => [
                'Domena 50–150 zł/rok, hosting 150–1500 zł/rok, SSL zwykle za darmo (Let\'s Encrypt), poczta Google/Microsoft 30–50 zł/mies.',
                'Opieka techniczna: 100–300 zł/mies. za stronę firmową, 300–800 zł za sklep; samodzielnie ok. 30 min miesięcznie',
                'Łącznie: wizytówka 250–500 zł/rok (sam) lub 1500–3500 zł (z opieką), sklep 1500–3000 lub 5000–12000 zł rocznie',
                'Nie oszczędzaj na hostingu za 60 zł, aktualizacjach (czyszczenie po włamaniu 1000–3000 zł) ani domenie na cudze dane',
            ],
        ];
    }
}
