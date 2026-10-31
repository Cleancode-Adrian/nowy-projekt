{{-- Google Tag Manager - jedyny tag analityczny w <head>. GA4 jest skonfigurowany
     wewnątrz kontenera GTM, więc nie ładujemy osobno gtag.js (podwójne liczenie odsłon). --}}
@php
    // Zdarzenie konwersji ustawiane flashem po udanej akcji po stronie serwera
    // (rejestracja, logowanie, publikacja zlecenia). Dzięki temu idzie do dataLayer
    // dokładnie raz - po realnym zapisie w bazie i po redirectcie, nie przy każdym
    // odświeżeniu strony.
    $dlEvent = session('dl_event');
    $dlEvent = is_array($dlEvent) && !empty($dlEvent['event']) ? $dlEvent : null;
@endphp
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-NXVNXNSM');</script>
<!-- End Google Tag Manager -->
@if($dlEvent)
<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({!! json_encode($dlEvent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!});
</script>
@endif
