<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Zdarzenia GA4 wysyłane z serwera (Measurement Protocol) - dla akcji, przy
 * których nie ma przeglądarki użytkownika, np. akceptacja zlecenia przez admina.
 *
 * Zdarzenie trafia do tego samego użytkownika GA4 dzięki client_id z ciasteczka
 * _ga zapisanemu przy wysłaniu zlecenia. Ciasteczko _ga istnieje tylko, gdy
 * użytkownik zgodził się na statystykę (Consent Mode) - bez zgody nie ma
 * client_id i nic nie wysyłamy.
 *
 * Wymaga GA4_API_SECRET (GA4 -> Administracja -> Strumienie danych -> strumień
 * -> Measurement Protocol API secrets). Bez niego wysyłka jest wyłączona.
 */
class Ga4MeasurementProtocol
{
    /** "GA1.1.1234567890.1700000000" -> "1234567890.1700000000" */
    public static function clientIdFromCookie(?string $cookie): ?string
    {
        if (!$cookie || !preg_match('/^GA\d\.\d\.(\d+\.\d+)$/', trim($cookie), $m)) {
            return null;
        }

        return $m[1];
    }

    public static function currentClientId(): ?string
    {
        return static::clientIdFromCookie(request()->cookie('_ga'));
    }

    public static function enabled(): bool
    {
        return (bool) (config('services.ga4.measurement_id') && config('services.ga4.api_secret'));
    }

    public static function send(?string $clientId, string $name, array $params = []): bool
    {
        if (!$clientId || !static::enabled()) {
            return false;
        }

        $url = sprintf(
            'https://www.google-analytics.com/mp/collect?measurement_id=%s&api_secret=%s',
            urlencode((string) config('services.ga4.measurement_id')),
            urlencode((string) config('services.ga4.api_secret')),
        );

        $body = [
            'client_id' => $clientId,
            'events' => [[
                'name' => $name,
                // engagement_time_msec - bez tego GA4 nie liczy zdarzenia do sesji/aktywności.
                'params' => array_filter($params, fn ($v) => $v !== null) + ['engagement_time_msec' => 1],
            ]],
        ];

        dispatch(function () use ($url, $body, $name) {
            try {
                $response = Http::timeout(5)->asJson()->post($url, $body);
                if ($response->failed()) {
                    Log::warning('GA4 MP: odrzucone zdarzenie', ['event' => $name, 'status' => $response->status()]);
                }
            } catch (\Throwable $e) {
                Log::warning('GA4 MP: błąd wysyłki - ' . $e->getMessage());
            }
        })->afterResponse();

        return true;
    }
}
