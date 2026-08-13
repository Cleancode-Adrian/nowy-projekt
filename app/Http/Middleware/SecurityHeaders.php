<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Dokłada nagłówki bezpieczeństwa do każdej odpowiedzi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );

        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // CSP wysyłamy tylko dla stron HTML - nie dla JSON-a, plików czy obrazków.
        if (config('security.csp_enabled') && $this->isHtmlResponse($response)) {
            $header = config('security.csp_report_only')
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';

            $response->headers->set($header, $this->contentSecurityPolicy($request));
        }

        return $response;
    }

    private function isHtmlResponse(Response $response): bool
    {
        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    /**
     * Uwaga: 'unsafe-inline' i 'unsafe-eval' są wymagane przez Alpine.js, Livewire
     * oraz inline'owe snippety GTM. Zdejmiemy je dopiero po przejściu na nonce.
     */
    private function contentSecurityPolicy(Request $request): string
    {
        $sources = config('security.csp_sources', []);

        $directives = [
            "default-src 'self'",
            $this->directive('script-src', ["'self'", "'unsafe-inline'", "'unsafe-eval'"], $sources['script'] ?? []),
            $this->directive('style-src', ["'self'", "'unsafe-inline'"], $sources['style'] ?? []),
            $this->directive('font-src', ["'self'", 'data:'], $sources['font'] ?? []),
            "img-src 'self' data: blob: https:",
            $this->directive('connect-src', ["'self'"], $sources['connect'] ?? []),
            $this->directive('frame-src', ["'self'"], $sources['frame'] ?? []),
            "media-src 'self'",
            "worker-src 'self' blob:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ];

        if ($request->secure()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }

    private function directive(string $name, array $defaults, array $extra): string
    {
        return $name . ' ' . implode(' ', array_unique(array_merge($defaults, $extra)));
    }
}
