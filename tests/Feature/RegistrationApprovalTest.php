<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Regresja: wszystkie nowe konta czekały na ręczną akceptację administratora,
 * więc osoba z kampanii reklamowej nie mogła się zalogować ani opublikować
 * zlecenia zaraz po rejestracji.
 */
class RegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function formData(array $overrides = []): array
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

    public function test_zleceniodawca_jest_aktywny_od_razu(): void
    {
        $this->post('/register', $this->formData());

        $this->assertDatabaseHas('users', [
            'email' => 'klient@example.com',
            'role' => 'client',
            'is_approved' => true,
        ]);
    }

    public function test_zleceniodawca_jest_zalogowany_po_rejestracji(): void
    {
        $response = $this->post('/register', $this->formData());

        $this->assertAuthenticated();
        $response->assertRedirect(route('announcements.create'));
    }

    public function test_wykonawca_nadal_czeka_na_akceptacje(): void
    {
        $this->post('/register', $this->formData([
            'email' => 'wykonawca@example.com',
            'role' => 'freelancer',
        ]));

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'email' => 'wykonawca@example.com',
            'role' => 'freelancer',
            'is_approved' => false,
        ]);
    }

    public function test_niezatwierdzony_wykonawca_nie_zaloguje_sie(): void
    {
        $user = User::factory()->freelancer()->pending()->create([
            'email' => 'oczekujacy@example.com',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_flaga_w_configu_wylacza_automatyczna_akceptacje(): void
    {
        config(['security.auto_approve_clients' => false]);

        $this->post('/register', $this->formData([
            'email' => 'reczny@example.com',
        ]));

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'email' => 'reczny@example.com',
            'is_approved' => false,
        ]);
    }

    public function test_rola_admina_nie_przechodzi_z_formularza(): void
    {
        $this->post('/register', $this->formData([
            'email' => 'intruz@example.com',
            'role' => 'admin',
        ]));

        $this->assertDatabaseMissing('users', ['email' => 'intruz@example.com']);
    }
}
