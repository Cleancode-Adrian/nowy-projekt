<?php

namespace Tests\Feature;

use App\Livewire\CreateAnnouncement;
use App\Models\Category;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Spam przez rejestrację: boty wpisywały w "Imię i nazwisko" reklamę z linkiem
 * i cudzy e-mail, a my wysyłaliśmy pod ten adres mail powitalny z tą treścią.
 */
class RegistrationSpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    private const SPAM_NAME = '* * * ACCOUNT ALERT: $3000 is reserved for your bonus account - proceed to https://eccwe.com/js/go-bv.php?x88hv6 * * *';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function enableTurnstile(): void
    {
        config([
            'services.turnstile.site_key' => 'site-key',
            'services.turnstile.secret_key' => 'secret-key',
        ]);
    }

    private function fakeCloudflare(bool $success): void
    {
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => $success, 'error-codes' => $success ? [] : ['invalid-input-response']]),
        ]);
    }

    private function formData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'password' => 'haslo-testowe-123',
            'password_confirmation' => 'haslo-testowe-123',
            'role' => 'freelancer',
            'accept_privacy' => '1',
            'accept_terms' => '1',
        ], $overrides);
    }

    public function test_reklama_z_linkiem_w_imieniu_jest_odrzucana(): void
    {
        $this->post('/register', $this->formData(['name' => self::SPAM_NAME]))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('users', ['email' => 'jan@example.com']);
        Mail::assertNothingSent();
    }

    public function test_polskie_imie_z_myslnikiem_przechodzi(): void
    {
        $this->post('/register', $this->formData(['name' => 'Łucja Żółć-Nowak']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'jan@example.com']);
    }

    public function test_link_w_nazwie_firmy_jest_odrzucany(): void
    {
        $this->post('/register', $this->formData(['company' => 'https://eccwe.com/promo']))
            ->assertSessionHasErrors('company');

        $this->post('/register', $this->formData(['company' => 'Firma.pl Sp. z o.o.']))
            ->assertSessionHasNoErrors();
    }

    public function test_honeypot_na_stronie_rejestracji(): void
    {
        $this->post('/register', $this->formData(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['email' => 'jan@example.com']);
        Mail::assertNothingSent();
    }

    public function test_bez_kluczy_turnstile_jest_wylaczony(): void
    {
        Http::fake();

        $this->get('/register')->assertOk()->assertDontSee('challenges.cloudflare.com');
        $this->post('/register', $this->formData())->assertSessionHasNoErrors();

        Http::assertNothingSent();
    }

    public function test_turnstile_widget_jest_na_stronie_rejestracji(): void
    {
        $this->enableTurnstile();

        $this->get('/register')
            ->assertOk()
            ->assertSee('data-sitekey="site-key"', false)
            ->assertSee('challenges.cloudflare.com/turnstile/v0/api.js', false);
    }

    public function test_turnstile_wymaga_tokenu(): void
    {
        $this->enableTurnstile();
        $this->fakeCloudflare(true);

        $this->post('/register', $this->formData())
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->assertDatabaseMissing('users', ['email' => 'jan@example.com']);
    }

    public function test_turnstile_odrzuca_bledny_token(): void
    {
        $this->enableTurnstile();
        $this->fakeCloudflare(false);

        $this->post('/register', $this->formData(['cf-turnstile-response' => 'zly-token']))
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->assertDatabaseMissing('users', ['email' => 'jan@example.com']);
    }

    public function test_turnstile_przepuszcza_poprawny_token(): void
    {
        $this->enableTurnstile();
        $this->fakeCloudflare(true);

        $this->post('/register', $this->formData(['cf-turnstile-response' => 'dobry-token']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'jan@example.com']);
        Http::assertSent(fn ($request) => $request['secret'] === 'secret-key' && $request['response'] === 'dobry-token');
    }

    public function test_awaria_cloudflare_nie_blokuje_rejestracji(): void
    {
        $this->enableTurnstile();
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $this->post('/register', $this->formData(['cf-turnstile-response' => 'token']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'jan@example.com']);
    }

    public function test_api_odrzuca_link_w_imieniu(): void
    {
        $this->postJson('/api/auth/register', $this->formData(['name' => self::SPAM_NAME]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    private function guestAccountStep()
    {
        $category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);

        return Livewire::test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', $category->id)
            ->call('next')
            ->set('reg_name', 'Anna Nowak')
            ->set('reg_email', 'anna@example.com')
            ->set('reg_password', 'haslo-testowe-123')
            ->set('reg_password_confirmation', 'haslo-testowe-123')
            ->set('reg_accept_terms', true)
            ->set('reg_accept_privacy', true);
    }

    public function test_formularz_zlecenia_wymaga_turnstile(): void
    {
        $this->enableTurnstile();
        $this->fakeCloudflare(true);

        $this->guestAccountStep()
            ->call('submit')
            ->assertHasErrors('turnstile_token')
            ->assertDispatched('turnstile-reset');

        $this->assertDatabaseMissing('users', ['email' => 'anna@example.com']);
        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_formularz_zlecenia_z_poprawnym_tokenem(): void
    {
        $this->enableTurnstile();
        $this->fakeCloudflare(true);

        $this->guestAccountStep()
            ->set('turnstile_token', 'dobry-token')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'anna@example.com']);
        $this->assertDatabaseCount('announcements', 1);
    }

    public function test_formularz_zlecenia_odrzuca_reklame_w_imieniu(): void
    {
        $this->guestAccountStep()
            ->set('reg_name', self::SPAM_NAME)
            ->call('submit')
            ->assertHasErrors('reg_name');

        $this->assertDatabaseMissing('users', ['email' => 'anna@example.com']);
    }
}
