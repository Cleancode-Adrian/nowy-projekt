<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new HtmlSanitizer();
    }

    public function test_usuwa_tag_script_razem_z_trescia(): void
    {
        $result = $this->sanitizer->clean('<p>Tekst</p><script>alert(1)</script>');

        $this->assertStringNotContainsString('script', $result);
        $this->assertStringNotContainsString('alert', $result);
        $this->assertStringContainsString('Tekst', $result);
    }

    public function test_usuwa_handlery_zdarzen(): void
    {
        $result = $this->sanitizer->clean('<img src="x.png" onerror="alert(1)" alt="x">');

        $this->assertStringNotContainsString('onerror', $result);
        $this->assertStringContainsString('x.png', $result);
    }

    public function test_usuwa_javascript_w_href(): void
    {
        $result = $this->sanitizer->clean('<a href="javascript:alert(1)">klik</a>');

        $this->assertStringNotContainsString('javascript', $result);
        $this->assertStringContainsString('klik', $result);
    }

    public function test_usuwa_javascript_rozbity_bialymi_znakami(): void
    {
        $result = $this->sanitizer->clean("<a href=\"java\nscript:alert(1)\">klik</a>");

        $this->assertStringNotContainsString('script:', $result);
    }

    public function test_usuwa_iframe_i_obiekty(): void
    {
        $result = $this->sanitizer->clean('<iframe src="//evil.tld"></iframe><object data="x"></object>');

        $this->assertStringNotContainsString('iframe', $result);
        $this->assertStringNotContainsString('object', $result);
    }

    public function test_usuwa_svg_z_osadzonym_skryptem(): void
    {
        $result = $this->sanitizer->clean('<svg><script>alert(1)</script></svg>');

        $this->assertStringNotContainsString('svg', $result);
        $this->assertStringNotContainsString('alert', $result);
    }

    public function test_zachowuje_dozwolone_formatowanie(): void
    {
        $html = '<h2>Nagłówek</h2><p><strong>Pogrubienie</strong> i <em>kursywa</em></p><ul><li>punkt</li></ul>';

        $result = $this->sanitizer->clean($html);

        $this->assertStringContainsString('<h2>', $result);
        $this->assertStringContainsString('<strong>', $result);
        $this->assertStringContainsString('<li>', $result);
    }

    public function test_zachowuje_polskie_znaki(): void
    {
        $result = $this->sanitizer->clean('<p>Zażółć gęślą jaźń</p>');

        $this->assertStringContainsString('Zażółć gęślą jaźń', $result);
    }

    public function test_dodaje_noopener_do_linkow_w_nowej_karcie(): void
    {
        $result = $this->sanitizer->clean('<a href="https://example.com" target="_blank">link</a>');

        $this->assertStringContainsString('noopener', $result);
    }

    public function test_pusta_wartosc_zwraca_pusty_string(): void
    {
        $this->assertSame('', $this->sanitizer->clean(null));
        $this->assertSame('', $this->sanitizer->clean('   '));
    }
}
