<?php

namespace Tests\Feature;

use App\Livewire\AnnouncementsList;
use App\Livewire\CreateAnnouncement;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class MetaPixelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        config(['services.meta_pixel.id' => '4642966532592189']);
    }

    public function test_pixel_startuje_bez_zgody_i_slucha_cookiebota(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString("fbq('init', \"4642966532592189\")", $html);
        $this->assertStringContainsString("fbq('track', 'PageView')", $html);

        // revoke przed init, a zgoda przychodzi z Cookiebota (kategoria marketing).
        $this->assertLessThan(strpos($html, "fbq('init'"), strpos($html, "fbq('consent', 'revoke')"));
        $this->assertStringContainsString('Cookiebot.consent.marketing', $html);
        $this->assertStringContainsString('CookiebotOnConsentReady', $html);

        // Bez noscript - piksel nie może strzelać przed zgodą.
        $this->assertStringNotContainsString('facebook.com/tr?id=', $html);
    }

    public function test_bez_id_pixel_jest_wylaczony(): void
    {
        config(['services.meta_pixel.id' => null]);

        $this->get('/')->assertOk()->assertDontSee('fbevents.js', false);
    }

    public function test_rejestracja_wysyla_complete_registration_raz(): void
    {
        Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        $this->post('/register', [
            'name' => 'Testowy Klient',
            'email' => 'klient@example.com',
            'password' => 'haslo-testowe-123',
            'password_confirmation' => 'haslo-testowe-123',
            'role' => 'client',
            'accept_privacy' => '1',
            'accept_terms' => '1',
        ])->assertRedirect(route('announcements.create'));

        $this->get(route('announcements.create'))
            ->assertSee("fbq('track', \"CompleteRegistration\", {\"status\":true,\"content_name\":\"klient\"}", false);

        $this->get(route('announcements.create'))
            ->assertDontSee('CompleteRegistration', false);
    }

    public function test_dodanie_zlecenia_wysyla_lead_z_budzetem(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        Livewire::actingAs($client)
            ->test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', $category->id)
            ->set('budget_min', 3000)
            ->set('budget_max', 5000)
            ->call('submit');

        $this->actingAs($client)->get(route('dashboard'))
            ->assertSee("fbq('track', \"Lead\", {\"content_category\":\"Strony WWW\",\"value\":5000,\"currency\":\"PLN\"}", false);
    }

    public function test_strona_zlecenia_wysyla_view_content_i_view_item(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);
        $announcement = Announcement::create([
            'user_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Sklep internetowy',
            'description' => 'Opis',
            'budget_max' => 8000,
            'status' => 'published',
            'is_approved' => true,
        ]);

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee("fbq('track', 'ViewContent', {\"content_ids\":[\"{$announcement->id}\"],\"content_type\":\"product\"", false)
            ->assertSee('"event":"view_item"', false);
    }

    public function test_filtr_wyszukiwania_wysyla_search(): void
    {
        Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        Livewire::test(AnnouncementsList::class)
            ->set('search', 'sklep')
            ->assertDispatched('fb-track', name: 'Search', data: ['search_string' => 'sklep'])
            ->assertDispatched('dl-event', payload: ['event' => 'search', 'search_term' => 'sklep'])
            ->set('category', 'strony-www')
            ->assertDispatched('fb-track', name: 'Search', data: ['search_string' => 'sklep', 'content_category' => 'Strony WWW']);
    }

    public function test_csp_dopuszcza_meta(): void
    {
        config(['security.csp_enabled' => true, 'security.csp_report_only' => false]);

        $csp = $this->get('https://projekciarz.test/')->headers->get('Content-Security-Policy');

        $this->assertMatchesRegularExpression('/script-src [^;]*https:\/\/connect\.facebook\.net/', $csp);
        $this->assertMatchesRegularExpression('/connect-src [^;]*https:\/\/www\.facebook\.com/', $csp);
    }
}
