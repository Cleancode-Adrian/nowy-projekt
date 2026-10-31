<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_jest_poprawnym_xml_i_nie_wstawia_sztucznych_dat(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = Category::create(['name' => 'Testowa', 'slug' => 'testowa']);

        $announcement = Announcement::create([
            'user_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Zlecenie w sitemapie',
            'description' => 'Opis',
            'status' => 'published',
            'is_approved' => true,
        ]);
        // Zlecenie zmodyfikowane tydzień temu - lastmod ma to odzwierciedlać, nie "teraz".
        $announcement->forceFill(['updated_at' => now()->subWeek()])->saveQuietly();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Sitemap nie jest poprawnym XML');

        $entries = [];
        foreach ($xml->url as $url) {
            $entries[(string) $url->loc] = isset($url->lastmod) ? (string) $url->lastmod : null;
        }

        $this->assertArrayHasKey(route('home'), $entries);
        $this->assertArrayHasKey(route('announcements.show', ['id' => $announcement->id]), $entries);

        // Strony bez sensownej daty zmiany nie dostają lastmod.
        $this->assertNull($entries[route('faq')]);
        $this->assertNull($entries[route('search.advanced')]);

        // Lista zleceń i strona główna dziedziczą datę ostatniej realnej zmiany treści.
        $expected = now()->subWeek()->toAtomString();
        $this->assertSame($expected, $entries[route('announcements.index')]);
        $this->assertSame($expected, $entries[route('home')]);
    }
}
