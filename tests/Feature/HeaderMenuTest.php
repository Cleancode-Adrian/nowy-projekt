<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresja: nagłówek miał na sztywno "Poradnik" -> /blog, a CMS dokładał
 * drugą pozycję "Blog" z route_name = blog.index. Dwa linki pod ten sam adres.
 */
class HeaderMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_pozycja_cms_pod_tym_samym_adresem_co_stala_nie_dubluje_sie(): void
    {
        Page::create([
            'title' => 'Blog (CMS)',
            'slug' => 'blog-cms',
            'route_name' => 'blog.index',
            'is_active' => true,
            'show_in_menu' => true,
            'menu_position' => 'header',
            'menu_order' => 1,
        ]);
        Page::create([
            'title' => 'Cennik usługi',
            'slug' => 'cennik-uslugi',
            'content' => '<p>Cennik</p>',
            'is_active' => true,
            'show_in_menu' => true,
            'menu_position' => 'header',
            'menu_order' => 2,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Blog');
        $response->assertDontSee('Poradnik');
        $response->assertDontSee('Blog (CMS)');
        // Inne strony CMS nadal trafiają do menu.
        $response->assertSee('Cennik usługi');
    }
}
