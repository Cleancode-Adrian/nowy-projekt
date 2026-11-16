<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cloudflare Turnstile - ochrona formularzy rejestracji przed botami.
 *
 * Bez kluczy w .env (lokalnie, w testach) weryfikacja jest wyłączona.
 * Gdy serwer Cloudflare nie odpowiada, przepuszczamy żądanie (zostają limity
 * prób i walidacja pól) - awaria po ich stronie nie może blokować rejestracji.
 */
class Turnstile
{
    public const FIELD = 'cf-turnstile-response';

    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function enabled(): bool
    {
        return filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public static function siteKey(): ?string
    {
        return config('services.turnstile.site_key');
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (!static::enabled()) {
            return true;
        }

        if (blank($token) || strlen($token) > 2048) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(self::VERIFY_URL, array_filter([
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $ip,
                ]));
        } catch (\Throwable $e) {
            Log::warning('Turnstile: brak połączenia z Cloudflare - przepuszczam', ['error' => $e->getMessage()]);
            return true;
        }

        if (!$response->successful()) {
            Log::warning('Turnstile: błąd HTTP ' . $response->status() . ' - przepuszczam');
            return true;
        }

        if ($response->json('success') === true) {
            return true;
        }

        Log::info('Turnstile: odrzucona weryfikacja', [
            'ip' => $ip,
            'errors' => $response->json('error-codes'),
        ]);

        return false;
    }
}
