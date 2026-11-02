<?php

namespace App\Livewire\Concerns;

/**
 * Zdarzenia analityczne z akcji Livewire, które nie robią redirectu.
 * Layout nasłuchuje na 'dl-event' i robi dataLayer.push(payload) - GTM
 * przekazuje to dalej do GA4 (zgodnie z Consent Mode).
 *
 * Akcje kończące się redirectem używają flasha session('dl_event').
 */
trait TracksEvents
{
    protected function trackEvent(string $event, array $params = []): void
    {
        $this->dispatch('dl-event', payload: ['event' => $event] + $params);
    }
}
