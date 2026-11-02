<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Zdarzenia Meta Pixel z backendu. Akcje kończące się redirectem wołają
 * FbPixel::flash() - layout (x-gtm-head) odpala je raz na następnej stronie.
 * Każde zdarzenie ma eventID, żeby przy ewentualnym Conversions API Meta
 * mogła zdeduplikować zdarzenie z przeglądarki i z serwera.
 */
class FbPixel
{
    public static function event(string $name, array $data = []): array
    {
        return ['name' => $name, 'data' => $data, 'id' => (string) Str::uuid()];
    }

    public static function flash(string $name, array $data = []): void
    {
        $events = session('fb_events', []);
        $events[] = static::event($name, $data);
        session()->flash('fb_events', $events);
    }

    /** @return list<array{name: string, data: array, id: string}> */
    public static function pending(): array
    {
        $events = session('fb_events', []);

        return array_values(array_filter(
            is_array($events) ? $events : [],
            fn ($e) => is_array($e) && !empty($e['name']) && is_string($e['name'])
        ));
    }
}
