<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-NXVNXNSM');</script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XY55LLSQMQ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-XY55LLSQMQ');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    @php
        $allowedCanonicalParams = ['category', 'q', 'minBudget', 'maxBudget', 'page'];
        $canonicalQuery = array_filter(
            request()->only($allowedCanonicalParams),
            fn ($value) => $value !== null && $value !== ''
        );
        ksort($canonicalQuery);
        $canonicalUrl = url()->current() . (empty($canonicalQuery) ? '' : '?' . http_build_query($canonicalQuery));
    @endphp
    <title>@yield('title', 'Projekciarz.pl - Platforma ogłoszeń dla wykonawców projektów')</title>
    <meta name="description" content="@yield('description', 'Znajdź idealnego wykonawcę dla swojego projektu. Tysiące zweryfikowanych specjalistów czeka na Twoje zlecenie.')">
    <meta name="keywords" content="@yield('keywords', 'projekty, zlecenia, ogłoszenia, wykonawcy, specjaliści, freelancer')">
    <meta name="author" content="Projekciarz.pl">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph (Facebook, LinkedIn) --}}
    <meta property="og:title" content="@yield('og_title', 'Projekciarz.pl')">
    <meta property="og:description" content="@yield('og_description', 'Platforma łącząca klientów z profesjonalnymi wykonawcami projektów')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Projekciarz.pl">
    <meta property="og:locale" content="pl_PL">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Projekciarz.pl')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Platforma łącząca klientów z profesjonalnymi wykonawcami projektów')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body class="antialiased">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NXVNXNSM" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    {{-- Navigation --}}
    @include('components.header')

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Newsletter Form --}}
    <section class="bg-gray-50 py-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto">
                <div id="mlb2-16123980" class="ml-form-embedContainer ml-subscribe-form ml-subscribe-form-16123980">
                    <div class="ml-form-align-center">
                        <div class="ml-form-embedWrapper embedForm">
                            <div class="ml-form-embedBody ml-form-embedBodyDefault row-form">
                                <div class="ml-form-embedContent" style="margin-bottom: 0px;">
                                    <h4 class="text-2xl font-bold text-gray-900 mb-2 text-center">Zapisz się do newslettera</h4>
                                    <p class="text-gray-600 text-center mb-6">Otrzymuj najnowsze informacje o projektach i możliwościach</p>
                                </div>
                                <form class="ml-block-form" action="https://assets.mailerlite.com/jsonp/1010939/forms/125829502731289626/subscribe" data-code="" method="post" target="_blank" id="newsletter-form">
                                    <div class="ml-form-formContent">
                                        <div class="ml-form-fieldRow">
                                            <div class="ml-field-group ml-field-email ml-validate-email ml-validate-required">
                                                <input aria-label="email" aria-required="true" type="email" class="form-control w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-inputmask="" name="fields[email]" placeholder="Twój adres email" autocomplete="email" required>
                                            </div>
                                        </div>
                                        <div class="ml-form-checkboxRow mt-4">
                                            <label class="flex items-start">
                                                <input type="checkbox" name="privacy_consent" value="1" required class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" id="privacy-consent-checkbox">
                                                <span class="text-sm text-gray-700">
                                                    Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z
                                                    <a href="{{ route('privacy-policy') }}" target="_blank" class="text-blue-600 hover:text-blue-700 underline font-medium">polityką prywatności</a> *
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="ml-submit" value="1">
                                    <div class="ml-form-embedSubmit mt-4">
                                        <button type="submit" class="primary w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="newsletter-submit-btn">
                                            Zapisz się
                                        </button>
                                        <button disabled="disabled" style="display: none;" type="button" class="loading">
                                            <div class="ml-form-embedSubmitLoad"></div>
                                            <span class="sr-only">Loading...</span>
                                        </button>
                                    </div>
                                    <input type="hidden" name="anticsrf" value="true">
                                </form>
                            </div>
                            <div class="ml-form-successBody row-success" style="display: none">
                                <div class="ml-form-successContent">
                                    <h4 class="text-2xl font-bold text-green-600 mb-2">Dziękujemy!</h4>
                                    <p class="text-gray-600">Zostałeś pomyślnie zapisany do naszego newslettera.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Notifications --}}
    <div x-data="{
             show: false,
             message: '',
             type: 'info',
             notify(msg, t = 'info') {
                 this.message = msg;
                 this.type = t;
                 this.show = true;
                 setTimeout(() => this.show = false, 3000);
             }
         }"
         x-show="show"
         x-transition
         @notify.window="notify($event.detail.message, $event.detail.type)"
         class="fixed top-4 right-4 z-50 max-w-sm">
        <div :class="{
            'bg-green-50 border-green-500 text-green-900': type === 'success',
            'bg-red-50 border-red-500 text-red-900': type === 'error',
            'bg-blue-50 border-blue-500 text-blue-900': type === 'info'
        }" class="border-l-4 p-4 rounded-lg shadow-lg">
            <p x-text="message"></p>
        </div>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Additional Scripts --}}
    @stack('scripts')

    {{-- MailerLite Newsletter Scripts --}}
    <style type="text/css">
        @import url("https://assets.mlcdn.com/fonts.css?version=1762785");

        .ml-form-embedSubmitLoad {
            display: inline-block;
            width: 20px;
            height: 20px;
        }

        .g-recaptcha {
            transform: scale(1);
            -webkit-transform: scale(1);
            transform-origin: 0 0;
            -webkit-transform-origin: 0 0;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            border: 0;
        }

        .ml-form-embedSubmitLoad:after {
            content: " ";
            display: block;
            width: 11px;
            height: 11px;
            margin: 1px;
            border-radius: 50%;
            border: 4px solid #fff;
            border-color: #ffffff #ffffff #ffffff transparent;
            animation: ml-form-embedSubmitLoad 1.2s linear infinite;
        }

        @keyframes ml-form-embedSubmitLoad {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #mlb2-16123980.ml-form-embedContainer {
            box-sizing: border-box;
            display: table;
            margin: 0 auto;
            position: static;
            width: 100% !important;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedWrapper {
            background-color: transparent;
            border-width: 0px;
            border-color: transparent;
            border-radius: 4px;
            border-style: solid;
            box-sizing: border-box;
            display: inline-block !important;
            margin: 0;
            padding: 0;
            position: relative;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedWrapper.embedForm {
            max-width: 400px;
            width: 100%;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-embedContent h4 {
            color: #000000;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-size: 30px;
            font-weight: 400;
            margin: 0 0 10px 0;
            text-align: left;
            word-break: break-word;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-embedContent p {
            color: #000000;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            margin: 0 0 10px 0;
            text-align: left;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-fieldRow input {
            background-color: #ffffff !important;
            color: #333333 !important;
            border-color: #cccccc;
            border-radius: 25px !important;
            border-style: solid !important;
            border-width: 1px !important;
            font-family: 'Inter', sans-serif;
            font-size: 14px !important;
            height: auto;
            line-height: 21px !important;
            margin-bottom: 0;
            margin-top: 0;
            padding: 10px 10px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            max-width: 100% !important;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-embedSubmit button {
            background-color: #2563eb !important;
            border: none !important;
            border-radius: 25px !important;
            box-shadow: none !important;
            color: #ffffff !important;
            cursor: pointer;
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            line-height: 21px !important;
            height: auto;
            padding: 10px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-embedSubmit button:hover {
            background-color: #1d4ed8 !important;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-embedSubmit button.loading {
            display: none;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-checkboxRow {
            margin: 0 0 20px 0;
            width: 100%;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-checkboxRow label {
            font-weight: normal;
            margin: 0;
            padding: 0;
            position: relative;
            display: flex;
            align-items: flex-start;
            cursor: pointer;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-checkboxRow input[type="checkbox"] {
            box-sizing: border-box;
            padding: 0;
            position: relative;
            z-index: 1;
            opacity: 1;
            margin-top: 4px;
            margin-right: 12px;
            cursor: pointer;
            flex-shrink: 0;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-checkboxRow label span {
            color: #333333;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 20px;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-checkboxRow label a {
            color: #2563eb;
            text-decoration: underline;
        }

        #mlb2-16123980.ml-form-embedContainer .ml-form-embedBody .ml-form-checkboxRow label a:hover {
            color: #1d4ed8;
        }

        .ml-error input {
            border-color: red !important;
        }

        .ml-error .ml-form-checkboxRow {
            border: 1px solid red !important;
            border-radius: 4px;
            padding: 10px;
        }

        @media only screen and (max-width: 400px) {
            .ml-form-embedWrapper.embedDefault,
            .ml-form-embedWrapper.embedPopup {
                width: 100% !important;
            }
        }
    </style>

    <script>
        function ml_webform_success_16123980() {
            var $ = ml_jQuery || jQuery;
            $('.ml-subscribe-form-16123980 .row-success').show();
            $('.ml-subscribe-form-16123980 .row-form').hide();
        }

        // Walidacja checkboxa przed wysłaniem formularza
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('newsletter-form');
            const checkbox = document.getElementById('privacy-consent-checkbox');
            const submitBtn = document.getElementById('newsletter-submit-btn');

            if (form && checkbox && submitBtn) {
                form.addEventListener('submit', function(e) {
                    if (!checkbox.checked) {
                        e.preventDefault();
                        alert('Musisz wyrazić zgodę na przetwarzanie danych osobowych, aby zapisać się do newslettera.');
                        checkbox.focus();
                        return false;
                    }
                });

                // Aktualizacja stanu przycisku w zależności od checkboxa
                checkbox.addEventListener('change', function() {
                    submitBtn.disabled = !this.checked;
                });

                // Początkowy stan przycisku
                submitBtn.disabled = !checkbox.checked;
            }
        });
    </script>

    <script src="https://groot.mailerlite.com/js/w/webforms.min.js?v176e10baa5e7ed80d35ae235be3d5024" type="text/javascript"></script>
    <script>
        fetch("https://assets.mailerlite.com/jsonp/1010939/forms/125829502731289626/takel");
    </script>
</body>
</html>

