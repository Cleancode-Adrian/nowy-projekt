<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogShowTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $overrides = []): BlogPost
    {
        $author = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        return BlogPost::create(array_merge([
            'author_id' => $author->id,
            'title' => 'Wpis testowy o wycenie strony',
            'slug' => 'wpis-testowy',
            'excerpt' => 'Zajawka testowa.',
            'tldr' => "- Wizytówka kosztuje 1500-4000 zł\nSklep od 10 000 zł\n\n• Zawsze proś o rozbicie wyceny",
            'content' => '<p>Wstęp.</p><h2>Ile kosztuje wizytówka?</h2><p>Tekst.</p><h3>Co wpływa na cenę</h3><p>Tekst.</p><h2>Ile kosztuje wizytówka?</h2><p>Powtórzony nagłówek.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(20),
        ], $overrides));
    }

    public function test_wpis_ma_tldr_spis_tresci_i_kotwice(): void
    {
        $post = $this->makePost();

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertOk();

        // TL;DR: prefiksy "-" i "•" oraz puste linie są ignorowane.
        $response->assertSee('TL;DR');
        $response->assertSee('Wizytówka kosztuje 1500-4000 zł');
        $response->assertSee('Zawsze proś o rozbicie wyceny');
        $response->assertDontSee('• Zawsze');

        // Kotwice na nagłówkach, duplikat dostaje sufiks.
        $response->assertSee('<h2 id="ile-kosztuje-wizytowka">', false);
        $response->assertSee('<h3 id="co-wplywa-na-cene">', false);
        $response->assertSee('<h2 id="ile-kosztuje-wizytowka-2">', false);

        // Spis treści linkuje do kotwic.
        $response->assertSee('href="#ile-kosztuje-wizytowka"', false);
        $response->assertSee('href="#co-wplywa-na-cene"', false);
        $response->assertSee('href="#ile-kosztuje-wizytowka-2"', false);
        $response->assertSee('Spis treści');
    }

    public function test_daty_publikacji_i_aktualizacji(): void
    {
        $post = $this->makePost();

        // Świeżo opublikowany wpis: tylko data publikacji.
        $this->get(route('blog.show', $post->slug))
            ->assertSee('Opublikowano')
            ->assertSee($post->published_at->format('d.m.Y'))
            ->assertDontSee('Zaktualizowano');

        // Wyświetlenia nie są aktualizacją treści.
        $updatedBefore = $post->fresh()->updated_at;
        $this->get(route('blog.show', $post->slug));
        $fresh = $post->fresh();
        $this->assertSame(2, $fresh->views_count);
        $this->assertNull($fresh->content_updated_at);
        $this->assertTrue($updatedBefore->equalTo($fresh->updated_at), 'Licznik wyświetleń podbił updated_at');

        // Zmiana treści po publikacji ustawia content_updated_at i pokazuje datę.
        $this->travel(5)->days();
        $fresh->update(['content' => $fresh->content . '<p>Dopisane.</p>']);

        $this->get(route('blog.show', $post->slug))
            ->assertSee('Zaktualizowano')
            ->assertSee(now()->format('d.m.Y'));
    }

    public function test_bez_tldr_i_naglowkow_nie_ma_pustych_sekcji(): void
    {
        $post = $this->makePost([
            'slug' => 'bez-tldr',
            'tldr' => null,
            'content' => '<p>Sam akapit, bez nagłówków.</p>',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertDontSee('TL;DR')
            ->assertDontSee('Spis treści');
    }

    public function test_og_image_dla_zewnetrznego_url_nie_jest_prefiksowany_storage(): void
    {
        $post = $this->makePost([
            'slug' => 'zewnetrzna-okladka',
            'featured_image' => 'https://images.unsplash.com/photo-1?w=1200',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertSee('property="og:image" content="https://images.unsplash.com/photo-1?w=1200"', false);
    }
}
