<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kanały prywatne (powiadomienia, wiadomości na żywo) muszą odrzucać próbę
 * podsłuchania cudzego strumienia - inaczej dowolny zalogowany użytkownik
 * mógłby nasłuchiwać powiadomień innej osoby przez Echo/Reverb.
 */
class BroadcastChannelsTest extends TestCase
{
    use RefreshDatabase;

    private function auth(User $user, string $channel): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($user)->post('/broadcasting/auth', [
            'channel_name' => 'private-' . $channel,
            'socket_id' => '1234.5678',
        ]);
    }

    public function test_wlasciciel_moze_nasluchiwac_swoich_powiadomien(): void
    {
        $user = User::factory()->create();

        $this->auth($user, "notifications.{$user->id}")->assertOk();
    }

    public function test_ktos_inny_nie_nasluchuje_cudzych_powiadomien(): void
    {
        $user = User::factory()->create();
        $intruz = User::factory()->create();

        $this->auth($intruz, "notifications.{$user->id}")->assertForbidden();
    }

    public function test_uczestnik_rozmowy_moze_nasluchiwac_kanalu_konwersacji(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $ids = collect([$a->id, $b->id])->sort()->values();

        $this->auth($a, "conversation.{$ids[0]}.{$ids[1]}")->assertOk();
        $this->auth($b, "conversation.{$ids[0]}.{$ids[1]}")->assertOk();
    }

    public function test_osoba_trzecia_nie_nasluchuje_cudzej_konwersacji(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $intruz = User::factory()->create();
        $ids = collect([$a->id, $b->id])->sort()->values();

        $this->auth($intruz, "conversation.{$ids[0]}.{$ids[1]}")->assertForbidden();
    }
}
