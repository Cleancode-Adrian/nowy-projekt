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
        $page->assertSee('{"event":"sign_up","user_role":"klient"}', false);

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
            ->assertSee('{"event":"sign_up","user_role":"wykonawca"}', false);
    }

    public function test_logowanie_wysyla_login(): void
    {
        $user = User::factory()->create(['role' => 'client', 'is_approved' => true]);

        $redirect = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $redirect->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('{"event":"login"}', false);

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
            ->assertSee('{"event":"generate_lead","lead_type":"zlecenie"}', false);
    }

    public function test_flash_z_nieprawidlowa_struktura_jest_ignorowany(): void
    {
        $this->withSession(['dl_event' => 'generate_lead'])
            ->get('/')
            ->assertOk()
            ->assertDontSee('dataLayer.push({"', false);
    }
}
