<?php

namespace Tests\Feature;

use App\Livewire\CreateAnnouncement;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * "Najpierw projekt, konto na końcu": gość z reklamy opisuje zlecenie,
 * a konto zakłada (albo loguje się) w ostatnim kroku.
 */
class GuestAnnouncementFlowTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);
    }

    private function filledProject()
    {
        return Livewire::test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', $this->category->id);
    }

    public function test_gosc_widzi_formularz_bez_logowania(): void
    {
        $this->get(route('announcements.create'))
            ->assertOk()
            ->assertSee('Dalej: konto i publikacja')
            ->assertSee("event: 'lead_form_start', lead_type: 'zlecenie'", false);
    }

    public function test_krok_2_wymaga_poprawnego_opisu_i_wysyla_login_wall_view(): void
    {
        Livewire::test(CreateAnnouncement::class)
            ->call('next')
            ->assertHasErrors(['title', 'description', 'category_id'])
            ->assertSet('step', 1);

        $this->filledProject()
            ->call('next')
            ->assertHasNoErrors()
            ->assertSet('step', 2)
            ->assertDispatched('dl-event', payload: ['event' => 'login_wall_view', 'link_location' => 'announcement_create'])
            ->assertSee('Załóż konto i opublikuj zlecenie');
    }

    public function test_gosc_zaklada_konto_i_publikuje_zlecenie_w_jednym_kroku(): void
    {
        $this->filledProject()
            ->call('next')
            ->set('reg_name', 'Anna Nowak')
            ->set('reg_email', 'anna@example.com')
            ->set('reg_password', 'haslo-testowe-123')
            ->set('reg_password_confirmation', 'haslo-testowe-123')
            ->set('reg_accept_terms', true)
            ->set('reg_accept_privacy', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $user = User::where('email', 'anna@example.com')->firstOrFail();
        $this->assertSame('client', $user->role);
        $this->assertTrue((bool) $user->is_approved);
        $this->assertAuthenticatedAs($user);

        $announcement = Announcement::firstOrFail();
        $this->assertSame($user->id, $announcement->user_id);
        $this->assertSame('pending', $announcement->status);

        // Na następnej stronie: sign_up + generate_lead (GA4) i CompleteRegistration + Lead (Meta).
        $this->get(route('dashboard'))
            ->assertSee('{"event":"sign_up","method":"email","user_role":"klient"}', false)
            ->assertSee('{"event":"generate_lead","lead_type":"zlecenie","item_category":"strony-www","value":0,"currency":"PLN"}', false)
            ->assertSee("fbq('track', \"CompleteRegistration\"", false)
            ->assertSee("{eventID: \"lead-{$announcement->id}\"}", false);
    }

    public function test_bledne_dane_konta_nie_zapisuja_zlecenia(): void
    {
        User::factory()->create(['email' => 'zajety@example.com']);

        $this->filledProject()
            ->call('next')
            ->set('reg_name', 'Anna')
            ->set('reg_email', 'zajety@example.com')
            ->set('reg_password', 'krotkie')
            ->set('reg_password_confirmation', 'inne')
            ->call('submit')
            ->assertHasErrors(['reg_email', 'reg_password', 'reg_accept_terms', 'reg_accept_privacy'])
            ->assertSet('step', 2);

        $this->assertDatabaseCount('announcements', 0);
        $this->assertGuest();
    }

    public function test_gosc_loguje_sie_istniejacym_kontem_klienta(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true, 'email' => 'klient@example.com']);

        $this->filledProject()
            ->call('next')
            ->call('setAuthMode', 'login')
            ->set('login_email', 'klient@example.com')
            ->set('login_password', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($client);
        $this->assertDatabaseHas('announcements', ['user_id' => $client->id, 'status' => 'pending']);

        $this->get(route('dashboard'))->assertSee('{"event":"login","method":"email"}', false);
    }

    public function test_konto_wykonawcy_nie_opublikuje_zlecenia_z_formularza_goscia(): void
    {
        User::factory()->create(['role' => 'freelancer', 'is_approved' => true, 'email' => 'wyk@example.com']);

        $this->filledProject()
            ->call('next')
            ->call('setAuthMode', 'login')
            ->set('login_email', 'wyk@example.com')
            ->set('login_password', 'password')
            ->call('submit')
            ->assertHasErrors(['login_email']);

        $this->assertGuest();
        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_zle_haslo_nie_loguje(): void
    {
        User::factory()->create(['role' => 'client', 'is_approved' => true, 'email' => 'klient@example.com']);

        $this->filledProject()
            ->call('next')
            ->call('setAuthMode', 'login')
            ->set('login_email', 'klient@example.com')
            ->set('login_password', 'zle-haslo')
            ->call('submit')
            ->assertHasErrors(['login_email']);

        $this->assertGuest();
        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_honeypot_blokuje_bota(): void
    {
        $this->filledProject()
            ->call('next')
            ->set('website', 'http://spam.example')
            ->set('reg_name', 'Bot')
            ->set('reg_email', 'bot@example.com')
            ->set('reg_password', 'haslo-testowe-123')
            ->set('reg_password_confirmation', 'haslo-testowe-123')
            ->set('reg_accept_terms', true)
            ->set('reg_accept_privacy', true)
            ->call('submit')
            ->assertHasErrors(['reg_email']);

        $this->assertDatabaseMissing('users', ['email' => 'bot@example.com']);
        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_bez_automatycznej_akceptacji_zlecenie_czeka_razem_z_kontem(): void
    {
        config(['security.auto_approve_clients' => false]);

        $this->filledProject()
            ->call('next')
            ->set('reg_name', 'Anna Nowak')
            ->set('reg_email', 'anna@example.com')
            ->set('reg_password', 'haslo-testowe-123')
            ->set('reg_password_confirmation', 'haslo-testowe-123')
            ->set('reg_accept_terms', true)
            ->set('reg_accept_privacy', true)
            ->call('submit')
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseHas('announcements', ['status' => 'pending']);
    }

    public function test_zalogowany_klient_publikuje_jak_dotad_bez_kroku_konta(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);

        Livewire::actingAs($client)
            ->test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', $this->category->id)
            ->call('next')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('announcements', ['user_id' => $client->id]);
    }

    public function test_login_wall_view_gdy_gosc_trafia_na_chroniona_strone(): void
    {
        $this->get('/wiadomosci')->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertSee('{"event":"login_wall_view","link_location":"wiadomosci"}', false);
    }

    public function test_flaga_wylacza_formularz_dla_gosci(): void
    {
        config(['security.guest_announcement_form' => false]);

        Livewire::test(CreateAnnouncement::class)->assertRedirect(route('login'));
    }

    public function test_fbclid_z_reklamy_trafia_do_capi_jako_fbc(): void
    {
        config([
            'services.meta_pixel.id' => '1128855549687710',
            'services.meta_pixel.capi_token' => 'test-token',
            'services.cookiebot.cbid' => 'fc720780-9d1c-4e63-a0c4-204d38173953',
        ]);
        Http::fake(['graph.facebook.com/*' => Http::response(['events_received' => 1])]);

        // Wejście z reklamy, zgoda marketingowa w Cookiebot, ale bez ciasteczka _fbc.
        $this->withUnencryptedCookie('CookieConsent', urlencode("{stamp:'x',necessary:true,marketing:true}"))
            ->get('/?fbclid=IwAR0abc123&utm_source=facebook&utm_medium=paid_social');

        $this->assertSame('IwAR0abc123', session('attribution.fbclid'));
        $this->assertSame(['utm_source' => 'facebook', 'utm_medium' => 'paid_social'], session('attribution.utm'));

        // Wywołanie poza żądaniem HTTP: zgoda z ciasteczka bieżącego requestu,
        // a wysyłka "po odpowiedzi" rusza dopiero przy terminate().
        request()->cookies->set('CookieConsent', urlencode("{stamp:'x',necessary:true,marketing:true}"));
        \App\Support\FbPixel::send(\App\Support\FbPixel::event('Lead', [], 'lead-1'));
        $this->app->terminate();

        Http::assertSent(function ($request) {
            $fbc = $request['data'][0]['user_data']['fbc'] ?? '';

            return $request['data'][0]['event_id'] === 'lead-1'
                && (bool) preg_match('/^fb\.1\.\d{13}\.IwAR0abc123$/', $fbc);
        });
    }

    public function test_linki_logowanie_rejestracja_przenosza_utm(): void
    {
        $this->get('/login?utm_source=facebook&utm_medium=paid_social&fbclid=IwAR0abc')
            ->assertSee(e(route('register', ['utm_source' => 'facebook', 'utm_medium' => 'paid_social', 'fbclid' => 'IwAR0abc'])), false);

        $this->get('/register')
            ->assertSee(e(route('login', ['utm_source' => 'facebook', 'utm_medium' => 'paid_social'])), false);
    }
}
