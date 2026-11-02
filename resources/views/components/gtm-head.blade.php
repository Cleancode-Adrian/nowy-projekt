{{-- Kolejność ma znaczenie:
     1. Consent Mode v2 - wartości domyślne (denied), zanim cokolwiek odpali.
     2. Cookiebot (CMP) - pokazuje baner i robi gtag('consent','update') po wyborze.
     3. Dane do dataLayer (rola użytkownika, zdarzenie z flasha).
     4. Google Tag Manager - GA4 i reszta tagów są w kontenerze; osobnego
        gtag.js nie ładujemy (podwójne liczenie odsłon). --}}
@php
    // Zdarzenie konwersji ustawiane flashem po udanej akcji po stronie serwera
    // (rejestracja, logowanie, publikacja zlecenia). Idzie do dataLayer dokładnie
    // raz - po realnym zapisie w bazie i po redirectcie.
    $dlEvent = session('dl_event');
    $dlEvent = is_array($dlEvent) && !empty($dlEvent['event']) ? $dlEvent : null;

    $userRole = auth()->check()
        ? match (auth()->user()->role) { 'client' => 'klient', 'freelancer' => 'wykonawca', default => auth()->user()->role }
        : 'gosc';

    $cookiebotId = config('services.cookiebot.cbid');
    $json = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
@endphp
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('consent', 'default', {
        ad_storage: 'denied',
        ad_user_data: 'denied',
        ad_personalization: 'denied',
        analytics_storage: 'denied',
        functionality_storage: 'denied',
        personalization_storage: 'denied',
        security_storage: 'granted',
        wait_for_update: 500
    });
    gtag('set', 'ads_data_redaction', true);
    gtag('set', 'url_passthrough', true);
</script>
@if($cookiebotId)
<script id="Cookiebot" src="https://consent.cookiebot.com/uc.js" data-cbid="{{ $cookiebotId }}" data-culture="{{ config('services.cookiebot.culture', 'PL') }}" type="text/javascript"></script>
@endif
@if($pixelId = config('services.meta_pixel.id'))
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
// RODO: nic nie wychodzi do Meta, dopóki użytkownik nie zgodzi się na cookies marketingowe.
fbq('consent', 'revoke');
fbq('init', {!! json_encode((string) $pixelId, $json) !!});
fbq('track', 'PageView');
@foreach(\App\Support\FbPixel::pending() as $fbEvent)
fbq('track', {!! json_encode($fbEvent['name'], $json) !!}, {!! json_encode((object) ($fbEvent['data'] ?? []), $json) !!}, {eventID: {!! json_encode((string) ($fbEvent['id'] ?? ''), $json) !!}});
@endforeach
// Zgoda marketingowa z Cookiebota -> Pixel.
function fbSyncConsent() {
    if (window.Cookiebot && Cookiebot.consent && Cookiebot.consent.marketing) {
        fbq('consent', 'grant');
    } else {
        fbq('consent', 'revoke');
    }
}
window.addEventListener('CookiebotOnConsentReady', fbSyncConsent);
window.addEventListener('CookiebotOnLoad', fbSyncConsent);
window.addEventListener('CookiebotOnAccept', fbSyncConsent);
window.addEventListener('CookiebotOnDecline', fbSyncConsent);
// uc.js ładuje się wcześniej - u powracającego użytkownika zgoda może być
// znana, zanim powyższe nasłuchy zostały podpięte.
if (window.Cookiebot && Cookiebot.consented) { fbSyncConsent(); }
</script>
<!-- End Meta Pixel -->
@endif
<script>
    window.dataLayer.push({!! json_encode(['user_role' => $userRole, 'logged_in' => auth()->check()], $json) !!});
@if($dlEvent)
    window.dataLayer.push({!! json_encode($dlEvent, $json) !!});
@endif
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-NXVNXNSM');</script>
<!-- End Google Tag Manager -->
