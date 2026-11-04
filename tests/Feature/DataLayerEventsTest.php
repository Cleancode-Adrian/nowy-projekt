<?php

namespace Tests\Feature;

use App\Livewire\CreateAnnouncement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Konwersje dla Google Tag Managera: zdarzenie trafia do dataLayer dokładnie raz,
 * po redirectcie z udanej akcji. GA4 żyje wewnątrz kontenera GTM, więc osobny
 * gtag.js w <head> podwajał odsłony - nie może wrócić.
 */
class DataLayerEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function registration(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Testowy Klient',
            'email' => 'klient@example.com',
            'password' => 'haslo-testowe-123',
            'password_confirmation' => 'haslo-testowe-123',
            'role' => 'client',
            'accept_privacy' => '1',
            'accept_terms' => '1',
        ], $overrides);
    }

    public function test_layout_laduje_gtm_bez_osobnego_gtag(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('googletagmanager.com/gtm.js', false);
        $response->assertSee("'GTM-NXVNXNSM'", false);
        $response->assertSee('ns.html?id=GTM-NXVNXNSM', false);
        $response->assertDontSee('gtag/js?id=', false);
        $response->assertDontSee("gtag('config'", false);
        // Bez flasha nie ma pusha zdarzenia.
        $response->assertDontSee('"event":"', false);
    }

    public function test_strony_auth_tez_maja_gtm(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('googletagmanager.com/gtm.js', false);
        $response->assertSee("'GTM-NXVNXNSM'", false);
        $response->assertDontSee('gtag/js?id=', false);
    }

    public function test_rejestracja_klienta_wysyla_sign_up_raz(): void
    {
        // Bez aktywnej kategorii formularz zlecenia przekierowuje na dashboard.
        Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        $redirect = $this->post('/register', $this->registration());
        $redirect->assertRedirect(route('announcements.create'));

        $page = $this->get(route('announcements.create'));
        $page->assertOk();
        $page->assertSee('{"event":"sign_up","method":"email","user_role":"klient"}', false);

        // Odświeżenie strony nie może wysłać konwersji drugi raz.
        $this->get(route('announcements.create'))
            ->assertDontSee('"event":"sign_up"', false);
    }

    public function test_rejestracja_wykonawcy_wysyla_sign_up_na_stronie_logowania(): void
    {
        $redirect = $this->post('/register', $this->registration([
            'email' => 'wykonawca@example.com',
            'role' => 'freelancer',
        ]));
        $redirect->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('{"event":"sign_up","method":"email","user_role":"wykonawca"}', false);
    }

    public function test_logowanie_wysyla_login(): void
    {
        $user = User::factory()->create(['role' => 'client', 'is_approved' => true]);

        $redirect = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $redirect->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('{"event":"login","method":"email"}', false);

        $this->get(route('dashboard'))
            ->assertDontSee('"event":"login"', false);
    }

    public function test_nieudane_logowanie_nie_wysyla_zdarzenia(): void
    {
        $user = User::factory()->create(['role' => 'client', 'is_approved' => true]);

        $this->post('/login', ['email' => $user->email, 'password' => 'zle-haslo']);

        $this->get(route('login'))->assertDontSee('"event":"login"', false);
    }

    public function test_publikacja_zlecenia_wysyla_generate_lead(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        Livewire::actingAs($client)
            ->test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', $category->id)
            ->call('submit')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('announcements', ['user_id' => $client->id, 'status' => 'pending']);

        $this->actingAs($client)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('{"event":"generate_lead","lead_type":"zlecenie","item_category":"strony-www","value":0,"currency":"PLN"}', false);
    }

    public function test_klikalny_telefon_i_sledzenie_phone_click(): void
    {
        $home = $this->get('/');

        $home->assertOk();
        $home->assertSee('href="tel:+48724441843"', false);
        $home->assertSee('data-phone-location="footer"', false);
        $home->assertSee('data-phone-location="mobile_menu"', false);
        $home->assertSee("'phone_click'", false);
        $home->assertSee('"telephone":"+48724441843"', false);

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('data-phone-location="faq"', false);
    }

    public function test_cookiebot_jest_pierwszym_skryptem_przed_gtm(): void
    {
        config(['services.cookiebot.cbid' => 'fc720780-9d1c-4e63-a0c4-204d38173953']);

        foreach (['/', '/login'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $cookiebot = strpos($html, '<script id="Cookiebot"');
            $gtm = strpos($html, 'googletagmanager.com/gtm.js');
            $this->assertNotFalse($cookiebot, "Brak Cookiebota na {$url}");
            $this->assertLessThan($gtm, $cookiebot, "Cookiebot musi być przed GTM na {$url}");
            // Consent Mode default -> Cookiebot -> GTM.
            $consent = strpos($html, "gtag('consent', 'default'");
            $this->assertNotFalse($consent, "Brak domyślnego Consent Mode na {$url}");
            $this->assertLessThan($cookiebot, $consent, "Consent default musi być przed Cookiebotem na {$url}");
            $this->assertStringContainsString("ad_user_data: 'denied'", $html);
            $this->assertStringContainsString('data-cbid="fc720780-9d1c-4e63-a0c4-204d38173953" data-culture="PL"', $html);
        }

        $this->get('/')->assertSee('Cookiebot.renew()', false);
    }

    public function test_bez_cbid_cookiebot_jest_wylaczony(): void
    {
        config(['services.cookiebot.cbid' => null]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="Cookiebot"', false)
            ->assertDontSee('Ustawienia cookies');
    }

    public function test_user_role_jest_w_datalayer_przed_gtm(): void
    {
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('{"user_role":"gosc","logged_in":false}', $html);
        $this->assertLessThan(strpos($html, 'googletagmanager.com/gtm.js'), strpos($html, '"user_role":"gosc"'));

        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $this->actingAs($client)->get('/')
            ->assertSee('{"user_role":"klient","logged_in":true}', false);
    }

    public function test_layout_nasluchuje_zdarzen_livewire_i_mailto(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee("window.addEventListener('dl-event'", false)
            ->assertSee("'email_click'", false);
    }

    public function test_zapis_ogloszenia_wysyla_save_announcement(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $freelancer = User::factory()->create(['role' => 'freelancer', 'is_approved' => true]);
        $category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);
        $announcement = \App\Models\Announcement::create([
            'user_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Strona dla gabinetu',
            'description' => 'Opis',
            'status' => 'published',
            'is_approved' => true,
        ]);

        Livewire::actingAs($freelancer)
            ->test(\App\Livewire\SaveButton::class, ['announcementId' => $announcement->id])
            ->call('toggle')
            ->assertDispatched('dl-event', payload: ['event' => 'save_announcement']);
    }

    public function test_flash_z_nieprawidlowa_struktura_jest_ignorowany(): void
    {
        $this->withSession(['dl_event' => 'generate_lead'])
            ->get('/')
            ->assertOk()
            ->assertDontSee('"event":', false);
    }
}
