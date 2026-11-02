<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Wyłącznik awaryjny: jeśli po wdrożeniu coś przestanie działać w przeglądarce,
    | ustaw SECURITY_CSP_ENABLED=false w .env i wyczyść cache configu.
    | Tryb "report only" pozwala zebrać naruszenia bez blokowania zasobów.
    |
    */

    'csp_enabled' => env('SECURITY_CSP_ENABLED', true),

    'csp_report_only' => env('SECURITY_CSP_REPORT_ONLY', false),

    /*
    | Zewnętrzne hosty, z których aplikacja realnie ładuje zasoby.
    | Każdy nowy skrypt/CDN trzeba tu dopisać, inaczej przeglądarka go zablokuje.
    */
    'csp_sources' => [
        // Google: wildcardy, bo GA4 dla ruchu z UE używa subdomen regionalnych
        // (region1.google-analytics.com), a GTM z włączonym server-side/consent
        // potrafi doładować skrypty z innych hostów niż www. Wpisy doubleclick /
        // googleadservices są pod przyszłe Google Ads (konwersje, remarketing).
        'script' => [
            'https://*.googletagmanager.com',
            'https://*.google-analytics.com',
            'https://www.google.com',
            'https://www.gstatic.com',
            'https://googleads.g.doubleclick.net',
            'https://www.googleadservices.com',
            'https://cdnjs.cloudflare.com',
            'https://groot.mailerlite.com',
            'https://assets.mailerlite.com',
            'https://assets.mlcdn.com',
            // Cookiebot CMP (uc.js + skrypty banera i deklaracji)
            'https://consent.cookiebot.com',
            'https://consentcdn.cookiebot.com',
        ],
        'style' => [
            'https://cdnjs.cloudflare.com',
            'https://fonts.googleapis.com',
            'https://fonts.bunny.net',
            'https://assets.mlcdn.com',
        ],
        'font' => [
            'https://cdnjs.cloudflare.com',
            'https://fonts.gstatic.com',
            'https://fonts.bunny.net',
            'https://assets.mlcdn.com',
        ],
        'connect' => [
            'https://*.google-analytics.com',
            'https://*.analytics.google.com',
            'https://*.googletagmanager.com',
            'https://*.g.doubleclick.net',
            'https://*.google.com',
            'https://assets.mailerlite.com',
            'https://groot.mailerlite.com',
            'https://consent.cookiebot.com',
            'https://consentcdn.cookiebot.com',
        ],
        'frame' => [
            'https://www.google.com',
            'https://*.googletagmanager.com',
            'https://td.doubleclick.net',
            'https://consentcdn.cookiebot.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Weryfikacja adresu e-mail
    |--------------------------------------------------------------------------
    |
    | Włącz DOPIERO po sprawdzeniu, że istniejący użytkownicy mają uzupełnione
    | users.email_verified_at - inaczej odetniesz ich od panelu. Backfill:
    |
    |   UPDATE users SET email_verified_at = created_at
    |   WHERE email_verified_at IS NULL AND is_approved = 1;
    |
    */

    'require_email_verification' => env('REQUIRE_EMAIL_VERIFICATION', false),

    /*
    |--------------------------------------------------------------------------
    | Automatyczna akceptacja zleceniodawców
    |--------------------------------------------------------------------------
    |
    | Konta z rolą "client" są aktywne od razu po rejestracji. Bez tego każdy
    | użytkownik z kampanii reklamowej utykał na komunikacie "konto oczekuje
    | na zatwierdzenie" i nie mógł opublikować zlecenia.
    |
    | Konta "freelancer" zawsze wymagają ręcznej akceptacji - to oni składają
    | oferty i piszą do klientów, więc moderacja ma tam sens.
    |
    | Ustaw false, jeśli chcesz wrócić do ręcznej weryfikacji wszystkich kont.
    |
    */

    'auto_approve_clients' => env('AUTO_APPROVE_CLIENTS', true),

    /*
    |--------------------------------------------------------------------------
    | Upload plików
    |--------------------------------------------------------------------------
    |
    | Jedno źródło prawdy dla wszystkich formularzy przyjmujących pliki.
    | Rozszerzenia wykonywalne NIGDY nie mogą trafić na tę listę.
    |
    */

    'uploads' => [
        'image_mimes' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'],
        'document_mimes' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip'],
        'max_image_kb' => 5120,
        'max_document_kb' => 10240,

        // Rozszerzenia blokowane bezwarunkowo, niezależnie od deklarowanego MIME.
        'blocked_extensions' => [
            'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phps', 'phar',
            'htaccess', 'htpasswd', 'ini', 'sh', 'bash', 'bat', 'cmd', 'com', 'exe',
            'dll', 'so', 'cgi', 'pl', 'py', 'rb', 'jsp', 'asp', 'aspx', 'svg', 'svgz',
            'html', 'htm', 'xhtml', 'js', 'mjs',
        ],
    ],

];
