<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Regresja: cała grupa /admin/* była kiedyś chroniona wyłącznie przez 'auth',
 * więc dowolny zalogowany klient mógł utworzyć sobie konto administratora.
 */
class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['/admin'],
            'lista użytkowników' => ['/admin/users'],
            'formularz nowego użytkownika' => ['/admin/users/create'],
            'ogłoszenia' => ['/admin/announcements'],
            'blog' => ['/admin/blog'],
            'generator bloga' => ['/admin/blog/generator'],
            'strony' => ['/admin/pages'],
            'media' => ['/admin/media'],
            'opinie' => ['/admin/ratings'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_zalogowany_klient_nie_wchodzi_do_panelu(string $url): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->get($url)->assertForbidden();
    }

    #[DataProvider('adminRoutes')]
    public function test_freelancer_nie_wchodzi_do_panelu(string $url): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $this->actingAs($freelancer)->get($url)->assertForbidden();
    }

    #[DataProvider('adminRoutes')]
    public function test_niezalogowany_jest_przekierowany(string $url): void
    {
        $this->get($url)->assertRedirect();
    }

    public function test_admin_wchodzi_do_panelu(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_niezatwierdzony_admin_nie_wchodzi_do_panelu(): void
    {
        $admin = User::factory()->admin()->pending()->create();

        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }

    public function test_klient_nie_tworzy_konta_administratora(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->post('/admin/users', [
            'name' => 'Intruz',
            'email' => 'intruz@example.com',
            'password' => 'haslo-testowe-123',
            'role' => 'admin',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'intruz@example.com']);
    }

    public function test_klient_nie_usuwa_uzytkownikow(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $ofiara = User::factory()->create();

        $this->actingAs($client)
            ->delete("/admin/users/{$ofiara->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $ofiara->id, 'deleted_at' => null]);
    }
}
