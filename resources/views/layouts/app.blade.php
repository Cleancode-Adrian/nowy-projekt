<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <x-gtm-head />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2f48e0">
    @auth
        <meta name="auth-user-id" content="{{ auth()->id() }}">
    @endauth

    {{-- SEO Meta Tags --}}
    @php
        $allowedCanonicalParams = ['category', 'q', 'minBudget', 'maxBudget', 'page'];
        $canonicalQuery = array_filter(
            request()->only($allowedCanonicalParams),
            fn ($value) => $value !== null && $value !== ''
        );
        ksort($canonicalQuery);
        $canonicalUrl = url()->current() . (empty($canonicalQuery) ? '' : '?' . http_build_query($canonicalQuery));

        // Komponenty Livewire przekazują meta przez ->layout('layouts.app', [...]),
        // czyli jako zmienne widoku - nie sekcje. Wcześniej layout czytał tylko @yield,
        // więc każda podstrona miała domyślny tytuł i opis. Obsługujemy oba źródła.
        $meta = function (string $key, string $default) use ($__data) {
            $value = $__data[$key] ?? null;
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
            $section = trim(app('view')->yieldContent($key));
            return $section !== '' ? $section : $default;
        };

        $metaTitle = $meta('title', 'Projekciarz.pl - Platforma ogłoszeń dla wykonawców projektów');
        // Strony z CMS mają sufiks już w meta_title, a komponent dokleja drugi - zostawiamy jeden.
        $metaTitle = preg_replace('/(\s*-\s*Projekciarz\.pl)+$/u', ' - Projekciarz.pl', $metaTitle);
        $metaDescription = $meta('description', 'Znajdź idealnego wykonawcę dla swojego projektu. Opisz zlecenie i zbierz oferty od zweryfikowanych specjalistów.');
        $metaKeywords = $meta('keywords', 'projekty, zlecenia, ogłoszenia, wykonawcy, specjaliści, freelancer');
        $ogTitle = $meta('og_title', $metaTitle);
        $ogDescription = $meta('og_description', $metaDescription);
        $ogImage = $meta('og_image', asset('images/og-default.jpg'));
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="Projekciarz.pl">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph (Facebook, LinkedIn) --}}
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Projekciarz.pl">
    <meta property="og:locale" content="pl_PL">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta('twitter_title', $ogTitle) }}">
    <meta name="twitter:description" content="{{ $meta('twitter_description', $ogDescription) }}">
    <meta name="twitter:image" content="{{ $meta('twitter_image', $ogImage) }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    {{-- Fonty --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

    {{-- Font Awesome (ikony w widokach treści) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body class="flex min-h-full flex-col antialiased">
<x-gtm-body />

    <a href="#tresc" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:shadow-lg">
        Przejdź do treści
    </a>

    {{-- Navigation --}}
    @include('components.header')

    {{-- Main Content --}}
    <main id="tresc" class="flex-1">
        {{ $slot }}
    </main>

    {{-- Newsletter (MailerLite) - klasy ml-* i struktura są wymagane przez skrypt webforms --}}
    <section class="border-t border-ink-100 bg-white" aria-labelledby="newsletter-heading">
        <div class="container-page py-12 lg:py-14">
            <div id="mlb2-16123980" class="ml-form-embedContainer ml-subscribe-form ml-subscribe-form-16123980">
                <div class="ml-form-embedWrapper embedForm">
                    <div class="ml-form-embedBody row-form grid items-center gap-8 lg:grid-cols-2">
                        <div>
                            <p class="eyebrow">Newsletter</p>
                            <h2 id="newsletter-heading" class="mt-2 text-2xl font-bold text-ink-900">Nowe zlecenia i poradniki raz w tygodniu</h2>
                            <p class="mt-2 text-ink-500">Przegląd świeżych projektów, orientacyjne stawki i praktyczne wskazówki. Bez spamu, wypisujesz się jednym kliknięciem.</p>
                        </div>

                        <form class="ml-block-form" action="https://assets.mailerlite.com/jsonp/1010939/forms/125829502731289626/subscribe" data-code="" method="post" target="_blank" id="newsletter-form">
                            <div class="ml-form-formContent">
                                <div class="ml-form-fieldRow flex flex-col gap-2 sm:flex-row">
                                    <div class="ml-field-group ml-field-email ml-validate-email ml-validate-required flex-1">
                                        <label for="newsletter-email" class="sr-only">Adres e-mail</label>
                                        <input id="newsletter-email" aria-required="true" type="email" class="input py-3" data-inputmask="" name="fields[email]" placeholder="twoj@email.pl" autocomplete="email" required>
                                    </div>
                                    <div class="ml-form-embedSubmit">
                                        <button type="submit" class="primary btn btn-primary w-full py-3 sm:w-auto" id="newsletter-submit-btn">Zapisz się</button>
                                        <button disabled="disabled" style="display: none;" type="button" class="loading btn btn-primary w-full py-3 sm:w-auto">
                                            <span class="ml-form-embedSubmitLoad"></span>
                                            <span class="sr-only">Wysyłanie…</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="ml-form-checkboxRow mt-3">
                                    <label class="flex items-start gap-2.5 text-sm text-ink-500">
                                        <input type="checkbox" name="privacy_consent" value="1" required class="mt-0.5 h-4 w-4 shrink-0 rounded border-ink-300 text-brand-600 focus:ring-brand-500" id="privacy-consent-checkbox">
                                        <span>
                                            Wyrażam zgodę na przetwarzanie danych osobowych zgodnie z
                                            <a href="{{ route('privacy-policy') }}" target="_blank" class="font-medium text-brand-600 underline underline-offset-2 hover:text-brand-700">polityką prywatności</a>.
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="ml-submit" value="1">
                            <input type="hidden" name="anticsrf" value="true">
                        </form>
                    </div>

                    <div class="ml-form-successBody row-success" style="display: none">
                        <div class="ml-form-successContent flex items-center gap-4 rounded-xl border border-emerald-200 bg-emerald-50 p-6">
                            <svg class="h-8 w-8 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                            <div>
                                <h4 class="font-semibold text-emerald-900">Dziękujemy za zapis!</h4>
                                <p class="text-sm text-emerald-800">Sprawdź skrzynkę i potwierdź adres e-mail.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Powiadomienia (toast) --}}
    <div x-data="{
             show: false,
             message: '',
             type: 'info',
             timer: null,
             notify(msg, t = 'info') {
                 this.message = msg;
                 this.type = t;
                 this.show = true;
                 clearTimeout(this.timer);
                 this.timer = setTimeout(() => this.show = false, 4000);
             }
         }"
         x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @notify.window="notify($event.detail.message, $event.detail.type)"
         class="fixed bottom-4 right-4 z-50 w-[calc(100%-2rem)] max-w-sm"
         role="status" aria-live="polite">
        <div class="flex items-start gap-3 rounded-xl border border-ink-100 bg-white p-4 shadow-xl">
            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                  :class="{
                      'bg-emerald-100 text-emerald-600': type === 'success',
                      'bg-red-100 text-red-600': type === 'error',
                      'bg-brand-100 text-brand-600': type !== 'success' && type !== 'error'
                  }">
                <svg x-show="type === 'success'" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                <svg x-show="type === 'error'" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                <svg x-show="type !== 'success' && type !== 'error'" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/></svg>
            </span>
            <p class="flex-1 text-sm text-ink-800" x-text="message"></p>
            <button type="button" @click="show = false" class="text-ink-400 hover:text-ink-700" aria-label="Zamknij">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
            </button>
        </div>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Additional Scripts --}}
    @stack('scripts')

    {{-- MailerLite - tylko style wymagane przez skrypt (stan ładowania, błędy walidacji) --}}
    <style>
        .ml-form-embedSubmit button.loading { display: none; }
        .ml-form-embedSubmitLoad {
            display: inline-block; width: 18px; height: 18px; border-radius: 9999px;
            border: 3px solid #fff; border-right-color: transparent;
            animation: ml-spin 0.9s linear infinite;
        }
        @keyframes ml-spin { to { transform: rotate(360deg); } }
        .ml-error input[type="email"] { border-color: #f87171 !important; }
        .ml-error .ml-form-checkboxRow label { color: #b91c1c; }
    </style>

    <script>
        // Callback sukcesu wywoływany przez webforms.min.js po zapisie przez AJAX.
        function ml_webform_success_16123980() {
            var $ = ml_jQuery || jQuery;
            $('.ml-subscribe-form-16123980 .row-success').show();
            $('.ml-subscribe-form-16123980 .row-form').hide();

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ event: 'newsletter_signup' });
        }

        // Przycisk aktywny dopiero po zaznaczeniu zgody RODO
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('newsletter-form');
            var checkbox = document.getElementById('privacy-consent-checkbox');
            var submitBtn = document.getElementById('newsletter-submit-btn');
            if (!form || !checkbox || !submitBtn) return;

            form.addEventListener('submit', function (e) {
                if (!checkbox.checked) {
                    e.preventDefault();
                    checkbox.focus();
                }
            });
            checkbox.addEventListener('change', function () { submitBtn.disabled = !this.checked; });
            submitBtn.disabled = !checkbox.checked;
        });
    </script>

    <script src="https://groot.mailerlite.com/js/w/webforms.min.js?v176e10baa5e7ed80d35ae235be3d5024" type="text/javascript"></script>
    <script>
        fetch("https://assets.mailerlite.com/jsonp/1010939/forms/125829502731289626/takel");
    </script>
</body>
</html>
