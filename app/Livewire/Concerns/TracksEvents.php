<?php

namespace App\Livewire\Concerns;

use App\Support\FbPixel;

/**
 * Zdarzenia analityczne z akcji Livewire, które nie robią redirectu.
 * Layout nasłuchuje na 'dl-event' (dataLayer -> GTM -> GA4) oraz 'fb-track'
 * (Meta Pixel). Akcje kończące się redirectem używają flasha:
 * session('dl_event') i FbPixel::flash().
 */
trait TracksEvents
{
    protected function trackEvent(string $event, array $params = []): void
    {
        $this->dispatch('dl-event', payload: ['event' => $event] + $params);
    }

    protected function trackFb(string $name, array $data = []): void
    {
        $event = FbPixel::event($name, $data);
        $this->dispatch('fb-track', name: $event['name'], data: $event['data'], id: $event['id']);

        // Wyszukiwanie zmienia się co klawisz - przez CAPI wysyłamy tylko
        // konkretne akcje (oferta, wiadomość), nie każdy stan filtra.
        if ($name !== 'Search') {
            FbPixel::send($event);
        }
    }

    /**
     * Wyszukiwanie: GA4 'search' + Meta 'Search'. Przeglądarka odpala je
     * z opóźnieniem i tylko ostatnie (filtry live zmieniają się co klawisz).
     */
    protected function trackSearch(?string $term, ?string $category = null): void
    {
        $term = trim((string) $term);
        if ($term === '' && !$category) {
            return;
        }

        $this->trackEvent('search', array_filter(['search_term' => $term, 'search_category' => $category]));
        $this->trackFb('Search', array_filter(['search_string' => $term, 'content_category' => $category]));
    }
}
