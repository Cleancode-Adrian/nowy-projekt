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
        config(['services.meta_pixel.id' => '1128855549687710']);
    }

    public function test_pixel_startuje_bez_zgody_i_slucha_cookiebota(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString("fbq('init', \"1128855549687710\")", $html);
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

    public function test_zgoda_grant_z_cookiebota_jawnie_i_dla_powracajacych(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString("fbq('consent', 'grant');", $html);
        $this->assertStringContainsString("addEventListener('CookiebotOnLoad', fbSyncConsent)", $html);
        $this->assertStringContainsString('if (window.Cookiebot && Cookiebot.consented) { fbSyncConsent(); }', $html);
    }

    public function test_wejscie_z_linku_z_filtrem_wysyla_search_od_razu(): void
    {
        Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        $this->get('/announcements?category=strony-www&q=sklep')
            ->assertOk()
            ->assertSee("fbq('track', 'Search', {\"search_string\":\"sklep\",\"content_category\":\"Strony WWW\"});", false)
            ->assertSee('{"event":"search","search_term":"sklep","search_category":"Strony WWW"}', false);

        $this->get('/announcements')
            ->assertOk()
            ->assertDontSee("fbq('track', 'Search'", false);
    }

    public function test_newsletter_wysyla_lead(): void
    {
        $this->get('/')->assertOk()
            ->assertSee("fbq('track', 'Lead', { content_name: 'Newsletter' }", false);
    }

    public function test_szukaj_z_parametrami_w_url_wysyla_search(): void
    {
        $cat = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        $this->get('/szukaj?q=wordpress&kategoria=' . $cat->id)
            ->assertOk()
            ->assertSee("fbq('track', 'Search', {\"search_string\":\"wordpress\",\"content_category\":\"Strony WWW\"});", false);

        $this->get('/szukaj')->assertOk()->assertDontSee("fbq('track', 'Search'", false);
    }

    public function test_capi_wysyla_to_samo_zdarzenie_z_tym_samym_event_id(): void
    {
        config(['services.meta_pixel.capi_token' => 'test-token']);
        \Illuminate\Support\Facades\Http::fake(['graph.facebook.com/*' => \Illuminate\Support\Facades\Http::response(['events_received' => 1])]);
        Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        $this->withUnencryptedCookie('CookieConsent', urlencode("{stamp:'abc',necessary:true,preferences:true,statistics:true,marketing:true,method:'explicit'}"))
            ->withUnencryptedCookie('_fbp', 'fb.1.1700000000000.123456789');

        $this->post('/register', [
            'name' => 'Testowy Klient',
            'phone' => '724 441 843',
            'email' => 'Klient@Example.com',
            'password' => 'haslo-testowe-123',
            'password_confirmation' => 'haslo-testowe-123',
            'role' => 'client',
            'accept_privacy' => '1',
            'accept_terms' => '1',
        ]);

        $browserId = collect(session('fb_events'))->firstWhere('name', 'CompleteRegistration')['id'];

        \Illuminate\Support\Facades\Http::assertSent(function ($request) use ($browserId) {
            $event = $request['data'][0] ?? [];

            return str_contains($request->url(), 'graph.facebook.com/v21.0/1128855549687710/events')
                && $request['access_token'] === 'test-token'
                && $event['event_name'] === 'CompleteRegistration'
                && $event['event_id'] === $browserId
                && $event['action_source'] === 'website'
                && $event['user_data']['em'] === [hash('sha256', 'klient@example.com')]
                && $event['user_data']['ph'] === [hash('sha256', '48724441843')]
                && $event['user_data']['fbp'] === 'fb.1.1700000000000.123456789';
        });
    }

    public function test_capi_bez_zgody_marketingowej_nic_nie_wysyla(): void
    {
        config(['services.meta_pixel.capi_token' => 'test-token']);
        \Illuminate\Support\Facades\Http::fake();

        // Odmowa w Cookiebot: marketing:false.
        $this->withUnencryptedCookie('CookieConsent', urlencode("{stamp:'abc',necessary:true,marketing:false}"))
            ->post('/register', [
                'name' => 'Testowy Klient',
                'email' => 'odmowa@example.com',
                'password' => 'haslo-testowe-123',
                'password_confirmation' => 'haslo-testowe-123',
                'role' => 'client',
                'accept_privacy' => '1',
                'accept_terms' => '1',
            ]);

        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_bez_tokenu_capi_nic_nie_wysyla(): void
    {
        config(['services.meta_pixel.capi_token' => null]);
        \Illuminate\Support\Facades\Http::fake();

        \App\Support\FbPixel::send(\App\Support\FbPixel::event('Lead'));

        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_csp_dopuszcza_meta(): void
    {
        config(['security.csp_enabled' => true, 'security.csp_report_only' => false]);

        $csp = $this->get('https://projekciarz.test/')->headers->get('Content-Security-Policy');

        $this->assertMatchesRegularExpression('/script-src [^;]*https:\/\/connect\.facebook\.net/', $csp);
        $this->assertMatchesRegularExpression('/connect-src [^;]*https:\/\/www\.facebook\.com/', $csp);
    }
}
