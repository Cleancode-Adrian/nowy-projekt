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
    <title>@yield('title', 'Projekciarz.pl - Platforma ogłoszeń dla wykonawców projektów')</title>
    <meta name="description" content="@yield('description', 'Znajdź idealnego wykonawcę dla swojego projektu. Tysiące zweryfikowanych specjalistów czeka na Twoje zlecenie.')">
    <meta name="keywords" content="@yield('keywords', 'projekty, zlecenia, ogłoszenia, wykonawcy, specjaliści, freelancer')">
    <meta name="author" content="Projekciarz.pl">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (Facebook, LinkedIn) --}}
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Platforma łącząca klientów z profesjonalnymi wykonawcami projektów')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Projekciarz.pl">
    <meta property="og:locale" content="pl_PL">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
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
</body>
</html>

