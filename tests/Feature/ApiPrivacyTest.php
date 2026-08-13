<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresja: publiczne endpointy zwracały pełny model User razem z e-mailem
 * i telefonem, co dawało każdemu gotową bazę kontaktów.
 */
class ApiPrivacyTest extends TestCase
{
    use RefreshDatabase;

    private function announcement(): Announcement
    {
        $owner = User::factory()->create([
            'email' => 'wlasciciel@example.com',
            'phone' => '+48 111 222 333',
        ]);

        $category = Category::create([
            'name' => 'Testowa',
            'slug' => 'testowa',
        ]);

        return Announcement::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Potrzebuję strony internetowej',
            'description' => 'Opis testowego zlecenia dla potrzeb testów.',
            'is_approved' => true,
            'status' => 'published',
        ]);
    }

    public function test_lista_ogloszen_nie_zwraca_danych_kontaktowych(): void
    {
        $this->announcement();

        $response = $this->getJson('/api/announcements');

        $response->assertOk();
        $response->assertDontSee('wlasciciel@example.com');
        $response->assertDontSee('111 222 333');
    }

    public function test_szczegoly_ogloszenia_nie_zwracaja_danych_kontaktowych(): void
    {
        $announcement = $this->announcement();

        $response = $this->getJson("/api/announcements/{$announcement->id}");

        $response->assertOk();
        $response->assertDontSee('wlasciciel@example.com');
        $response->assertDontSee('111 222 333');
    }

    public function test_uzytkownik_widzi_wlasny_email_w_auth_me(): void
    {
        $user = User::factory()->create(['email' => 'ja@example.com']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'ja@example.com');
    }

    public function test_haslo_nigdy_nie_wychodzi_z_api(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonMissingPath('user.password');
    }

    public function test_rejestracja_przez_api_nie_pozwala_ustawic_roli_admina(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Intruz',
            'email' => 'intruz@example.com',
            'password' => 'haslo-testowe-123',
            'password_confirmation' => 'haslo-testowe-123',
            'role' => 'admin',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('users', ['email' => 'intruz@example.com']);
    }
}
