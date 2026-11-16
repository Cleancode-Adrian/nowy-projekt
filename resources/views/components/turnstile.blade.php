@props([
    // Nazwa właściwości Livewire, do której trafia token. Bez niej widżet
    // dokłada do zwykłego formularza ukryte pole cf-turnstile-response.
    'model' => null,
])

@if(\App\Services\Turnstile::enabled())
    @if($model)
        {{-- Livewire: krok z widżetem pojawia się po morphie, a Livewire nie wykonuje
             wstrzykniętych <script>, więc skrypt Cloudflare doładowujemy sami. --}}
        <div {{ $attributes->merge(['class' => 'min-h-[65px]']) }}
             wire:ignore
             x-data="{ widgetId: null }"
             x-init="
                const render = () => {
                    if (widgetId !== null || !$el.isConnected) return;
                    widgetId = window.turnstile.render($el, {
                        sitekey: @js(\App\Services\Turnstile::siteKey()),
                        language: 'pl',
                        callback: (token) => { $wire.{{ $model }} = token; },
                        'expired-callback': () => { $wire.{{ $model }} = ''; },
                        'error-callback': () => { $wire.{{ $model }} = ''; },
                    });
                };
                if (window.turnstile) {
                    render();
                } else {
                    window.addEventListener('turnstile-ready', render, { once: true });
                }
                if (!window.turnstile && !document.getElementById('cf-turnstile-api')) {
                    window.onTurnstileReady = () => window.dispatchEvent(new Event('turnstile-ready'));
                    const s = document.createElement('script');
                    s.id = 'cf-turnstile-api';
                    s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=onTurnstileReady';
                    s.async = true;
                    document.head.appendChild(s);
                }
             "
             x-on:turnstile-reset.window="if (widgetId !== null && window.turnstile) { window.turnstile.reset(widgetId); $wire.{{ $model }} = ''; }"
        ></div>
    @else
        <div {{ $attributes->merge(['class' => 'cf-turnstile min-h-[65px]']) }}
             data-sitekey="{{ \App\Services\Turnstile::siteKey() }}"
             data-language="pl"></div>
        @once
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endonce
    @endif
@endif
