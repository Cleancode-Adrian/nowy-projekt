<?php

namespace App\Support;

/**
 * Zdarzenia dataLayer (GTM -> GA4) z akcji kończących się redirectem.
 * Kilka zdarzeń może iść na raz (np. sign_up + generate_lead po założeniu
 * konta w formularzu zlecenia) - layout (x-gtm-head) odpala je po kolei.
 */
class DataLayer
{
    public static function flash(array $event): void
    {
        if (empty($event['event'])) {
            return;
        }

        $events = session('dl_events', []);
        $events[] = $event;
        session()->flash('dl_events', $events);
    }

    /** @return list<array> wszystkie oczekujące zdarzenia (stary klucz dl_event + lista dl_events) */
    public static function pending(): array
    {
        $events = [];

        $single = session('dl_event');
        if (is_array($single) && !empty($single['event'])) {
            $events[] = $single;
        }

        foreach ((array) session('dl_events', []) as $event) {
            if (is_array($event) && !empty($event['event'])) {
                $events[] = $event;
            }
        }

        return $events;
    }
}
