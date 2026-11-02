<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Zdarzenia Meta Pixel z backendu.
 *
 * - flash(): akcje kończące się redirectem - layout (x-gtm-head) odpala je raz
 *   na następnej stronie.
 * - send(): to samo zdarzenie wysyłane z serwera przez Conversions API
 *   (z tym samym event_id), żeby Meta nie zgubiła go przez adblock. Działa
 *   tylko, gdy jest ustawiony META_CAPI_TOKEN, i dopiero po wysłaniu
 *   odpowiedzi do przeglądarki.
 */
class FbPixel
{
    public static function event(string $name, array $data = []): array
    {
        return ['name' => $name, 'data' => $data, 'id' => (string) Str::uuid()];
    }

    /**
     * $user - gdy zdarzenie dotyczy kogoś, kto nie jest jeszcze zalogowany
     * (rejestracja), a chcemy przekazać jego e-mail do dopasowania w CAPI.
     */
    public static function flash(string $name, array $data = [], $user = null): void
    {
        $event = static::event($name, $data);

        $events = session('fb_events', []);
        $events[] = $event;
        session()->flash('fb_events', $events);

        static::send($event, $user);
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

    /**
     * Conversions API. Zbieramy dane z requestu teraz (po odpowiedzi request
     * bywa już niedostępny), a samo żądanie HTTP robimy po wysłaniu strony.
     */
    public static function send(array $event, $user = null): void
    {
        $token = config('services.meta_pixel.capi_token');
        $pixelId = config('services.meta_pixel.id');

        // RODO: CAPI omija przeglądarkę, więc zgodę sprawdzamy sami.
        if (!$token || !$pixelId || !static::hasMarketingConsent()) {
            return;
        }

        $payload = static::capiPayload($event, $user);
        $testCode = config('services.meta_pixel.capi_test_code');
        $version = config('services.meta_pixel.graph_version', 'v21.0');

        dispatch(function () use ($payload, $token, $pixelId, $testCode, $version) {
            try {
                $body = ['data' => [$payload], 'access_token' => $token];
                if ($testCode) {
                    $body['test_event_code'] = $testCode;
                }

                $response = Http::timeout(5)->asJson()
                    ->post("https://graph.facebook.com/{$version}/{$pixelId}/events", $body);

                if ($response->failed()) {
                    Log::warning('Meta CAPI: odrzucone zdarzenie', [
                        'event' => $payload['event_name'],
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Meta CAPI: błąd wysyłki - ' . $e->getMessage());
            }
        })->afterResponse();
    }

    /**
     * Zgoda na cookies marketingowe zapisana przez Cookiebota w ciasteczku
     * CookieConsent, np. {stamp:'…',necessary:true,…,marketing:true,…}.
     * Bez Cookiebota (brak CBID) nie ma skąd wziąć zgody - nie wysyłamy.
     */
    public static function hasMarketingConsent(): bool
    {
        if (!config('services.cookiebot.cbid')) {
            return false;
        }

        $consent = urldecode((string) request()->cookie('CookieConsent', ''));

        return (bool) preg_match('/\bmarketing\s*:\s*true\b/i', $consent);
    }

    /** Telefon w formacie E.164 bez "+": polskie 9 cyfr dostają 48 z przodu. */
    private static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        return strlen($digits) === 9 ? '48' . $digits : ltrim($digits, '0');
    }

    /**
     * Dane użytkownika haszujemy SHA-256 (wymóg Meta). IP i user agent idą
     * jawnie - tak każe specyfikacja CAPI.
     */
    public static function capiPayload(array $event, $user = null): array
    {
        $request = request();
        $user ??= auth()->user();

        $userData = array_filter([
            'em' => $user?->email ? [hash('sha256', mb_strtolower(trim($user->email)))] : null,
            'ph' => ($phone = static::normalizePhone($user?->phone)) ? [hash('sha256', $phone)] : null,
            'external_id' => $user ? [hash('sha256', (string) $user->id)] : null,
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
        ]);

        return array_filter([
            'event_name' => $event['name'],
            'event_time' => time(),
            'event_id' => $event['id'],
            'action_source' => 'website',
            'event_source_url' => $request->headers->get('referer') ?: $request->fullUrl(),
            'user_data' => $userData,
            'custom_data' => $event['data'] ?: null,
        ]);
    }
}
