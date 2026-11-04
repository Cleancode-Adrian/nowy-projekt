<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Zapamiętuje w sesji źródło wejścia z reklamy (utm_*, fbclid, gclid), żeby
 * nie zgubić go po drodze przez logowanie/rejestrację:
 * - fbclid -> fbc dla Meta Conversions API (FbPixel::fbcFromSession),
 * - utm_* -> przenoszone w linkach logowanie <-> rejestracja.
 * Ostatnie kliknięcie w reklamę nadpisuje poprzednie.
 */
class CaptureAttribution
{
    public const UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->hasSession()) {
            $fbclid = $this->clean($request->query('fbclid'));
            if ($fbclid) {
                $request->session()->put('attribution.fbclid', $fbclid);
                $request->session()->put('attribution.fbclid_at', (int) round(microtime(true) * 1000));
            }

            $gclid = $this->clean($request->query('gclid'));
            if ($gclid) {
                $request->session()->put('attribution.gclid', $gclid);
            }

            $utm = [];
            foreach (self::UTM_KEYS as $key) {
                if ($value = $this->clean($request->query($key))) {
                    $utm[$key] = $value;
                }
            }
            if ($utm) {
                $request->session()->put('attribution.utm', $utm);
            }
        }

        return $next($request);
    }

    private function clean(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        // Tylko bezpieczne znaki, z limitem długości - to trafia do linków i do Meta.
        return $value !== '' && strlen($value) <= 500 && preg_match('/^[A-Za-z0-9._\-~%|:+]+$/', $value) ? $value : null;
    }

    /** Parametry do doklejenia do linków logowanie <-> rejestracja. */
    public static function linkParams(Request $request): array
    {
        $params = array_filter($request->only([...self::UTM_KEYS, 'fbclid', 'gclid']), 'is_string');

        return $params ?: (array) $request->session()->get('attribution.utm', []);
    }
}
