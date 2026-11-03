<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresja: rejestracja z nieaktualnym tokenem CSRF (strona otwarta długo,
 * powrót "Wstecz") kończyła się pustą stroną "419 Page Expired".
 */
class ExpiredSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stary_token_przy_rejestracji_wraca_do_formularza_z_danymi(): void
    {
        // W testach Laravel domyślnie pomija CSRF - tu chcemy prawdziwą weryfikację.
        $this->app->instance('env', 'production');

        $response = $this->from('/register')->post('/register', [
            '_token' => 'nieaktualny-token',
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'password' => 'tajne-haslo-123',
            'password_confirmation' => 'tajne-haslo-123',
            'role' => 'client',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasInput('name', 'Jan Kowalski');
        $response->assertSessionHasInput('email', 'jan@example.com');
        $response->assertSessionMissing('_old_input.password');

        $this->get('/register')->assertOk()
            ->assertSee('Sesja wygasła')
            ->assertSee('value="Jan Kowalski"', false);
    }

    public function test_livewire_i_json_dalej_dostaja_419(): void
    {
        $this->app->instance('env', 'production');

        $this->postJson('/register', ['_token' => 'zly'])->assertStatus(419);
    }
}
