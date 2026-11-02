<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresja: CSP dopuszczało tylko www.google-analytics.com, a GA4 dla ruchu
 * z UE wysyła hity na region1.google-analytics.com / region1.analytics.google.com.
 * Przeglądarka blokowała każde żądanie /g/collect i GA4 nie dostawało danych.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    private function csp(): string
    {
        config(['security.csp_enabled' => true, 'security.csp_report_only' => false]);

        $response = $this->get('https://projekciarz.test/');
        $response->assertOk();

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp, 'Brak nagłówka Content-Security-Policy');

        return $csp;
    }

    private function directive(string $csp, string $name): string
    {
        foreach (explode(';', $csp) as $part) {
            $part = trim($part);
            if (str_starts_with($part, $name . ' ')) {
                return $part;
            }
        }

        $this->fail("Brak dyrektywy {$name} w CSP");
    }

    public function test_connect_src_dopuszcza_regionalne_hosty_ga4(): void
    {
        $connect = $this->directive($this->csp(), 'connect-src');

        $this->assertStringContainsString('https://*.google-analytics.com', $connect);
        $this->assertStringContainsString('https://*.analytics.google.com', $connect);
        $this->assertStringContainsString('https://*.googletagmanager.com', $connect);
    }

    public function test_script_i_frame_src_dopuszczaja_gtm_i_google_ads(): void
    {
        $csp = $this->csp();

        $script = $this->directive($csp, 'script-src');
        $this->assertStringContainsString('https://*.googletagmanager.com', $script);
        $this->assertStringContainsString('https://googleads.g.doubleclick.net', $script);

        $frame = $this->directive($csp, 'frame-src');
        $this->assertStringContainsString('https://*.googletagmanager.com', $frame);
        $this->assertStringContainsString('https://td.doubleclick.net', $frame);
    }

    public function test_csp_dopuszcza_cookiebot(): void
    {
        $csp = $this->csp();

        $this->assertStringContainsString('https://consent.cookiebot.com', $this->directive($csp, 'script-src'));
        $this->assertStringContainsString('https://consentcdn.cookiebot.com', $this->directive($csp, 'script-src'));
        $this->assertStringContainsString('https://consentcdn.cookiebot.com', $this->directive($csp, 'connect-src'));
        $this->assertStringContainsString('https://consentcdn.cookiebot.com', $this->directive($csp, 'frame-src'));
    }

    public function test_csp_nie_jest_wysylane_dla_odpowiedzi_innych_niz_html(): void
    {
        config(['security.csp_enabled' => true]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertNull($response->headers->get('Content-Security-Policy'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
    }
}
