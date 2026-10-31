<?php

namespace Tests\Feature;

use App\Livewire\ProposalForm;
use App\Livewire\ProposalsList;
use App\Livewire\RateUser;
use App\Livewire\ShowMessages;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Proposal;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresje z audytu bezpieczeństwa: IDOR-y w komponentach Livewire i API.
 */
class AuthorizationHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function announcement(User $owner, array $attrs = []): Announcement
    {
        $category = Category::firstOrCreate(['slug' => 'testowa'], ['name' => 'Testowa']);

        return Announcement::create(array_merge([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Testowe ogłoszenie',
            'description' => 'Opis',
            'budget_currency' => 'PLN',
            'status' => 'published',
            'is_approved' => true,
        ], $attrs));
    }

    private function proposal(Announcement $announcement, User $freelancer, string $status = 'pending'): Proposal
    {
        return Proposal::create([
            'announcement_id' => $announcement->id,
            'user_id' => $freelancer->id,
            'price' => 1000,
            'currency' => 'PLN',
            'delivery_days' => 7,
            'description' => 'Oferta testowa',
            'status' => $status,
        ]);
    }

    public function test_nie_mozna_podmienic_id_ogloszenia_w_liscie_ofert(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $mine = $this->announcement($owner);
        $foreign = $this->announcement($other);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($owner)
            ->test(ProposalsList::class, ['announcementId' => $mine->id])
            ->set('announcementId', $foreign->id);
    }

    public function test_zleceniodawca_nie_oceni_osoby_spoza_zlecenia(): void
    {
        Mail::fake();
        $owner = User::factory()->create();
        $stranger = User::factory()->freelancer()->create();
        $announcement = $this->announcement($owner);

        Livewire::actingAs($owner)
            ->test(RateUser::class, ['user' => $stranger, 'announcement' => $announcement])
            ->call('submit');

        $this->assertSame(0, Rating::count());
    }

    public function test_zleceniodawca_oceni_zaakceptowanego_wykonawce_tylko_raz(): void
    {
        Mail::fake();
        $owner = User::factory()->create();
        $freelancer = User::factory()->freelancer()->create();
        $announcement = $this->announcement($owner);
        $this->proposal($announcement, $freelancer, 'accepted');

        $component = Livewire::actingAs($owner)
            ->test(RateUser::class, ['user' => $freelancer, 'announcement' => $announcement]);

        $component->call('submit');
        // Klient "resetuje" flagę i próbuje ponownie.
        $component->set('hasRated', false)->call('submit');

        $this->assertSame(1, Rating::count());
    }

    public function test_api_nie_pozwala_ocenic_dowolnego_uzytkownika(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->freelancer()->create();
        $announcement = $this->announcement($owner);

        Sanctum::actingAs($owner);

        $this->postJson('/api/ratings', [
            'announcement_id' => $announcement->id,
            'rated_id' => $stranger->id,
            'rating' => 1,
        ])->assertStatus(422);

        // Ocena samego siebie (rated_id jako string z requestu).
        $this->postJson('/api/ratings', [
            'announcement_id' => $announcement->id,
            'rated_id' => (string) $owner->id,
            'rating' => 5,
        ])->assertStatus(422);

        $this->assertSame(0, Rating::count());
    }

    public function test_api_nie_pokazuje_niezaakceptowanych_opinii(): void
    {
        $owner = User::factory()->create();
        $freelancer = User::factory()->freelancer()->create();
        $announcement = $this->announcement($owner);

        Rating::create([
            'announcement_id' => $announcement->id,
            'rater_id' => $owner->id,
            'rated_id' => $freelancer->id,
            'rating' => 1,
            'comment' => 'Niezmoderowana',
            'is_approved' => false,
        ]);

        $this->getJson("/api/users/{$freelancer->id}/ratings")
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_nie_mozna_zlozyc_oferty_do_nieopublikowanego_ogloszenia(): void
    {
        Mail::fake();
        $owner = User::factory()->create();
        $freelancer = User::factory()->freelancer()->create();
        $pending = $this->announcement($owner, ['status' => 'pending', 'is_approved' => false]);

        Livewire::actingAs($freelancer)
            ->test(ProposalForm::class, ['announcement' => $pending])
            ->set('price', 100)
            ->set('delivery_days', 5)
            ->set('description', str_repeat('Rzetelna oferta. ', 5))
            ->call('submit');

        $this->assertSame(0, Proposal::count());
    }

    public function test_nie_mozna_zlozyc_drugiej_oferty_resetujac_flage(): void
    {
        Mail::fake();
        $owner = User::factory()->create();
        $freelancer = User::factory()->freelancer()->create();
        $announcement = $this->announcement($owner);

        $component = Livewire::actingAs($freelancer)
            ->test(ProposalForm::class, ['announcement' => $announcement]);

        foreach ([1, 2] as $_) {
            $component->set('hasProposal', false)
                ->set('price', 100)
                ->set('delivery_days', 5)
                ->set('description', str_repeat('Rzetelna oferta. ', 5))
                ->call('submit');
        }

        $this->assertSame(1, Proposal::count());
    }

    public function test_wysylanie_wiadomosci_ma_limit(): void
    {
        Mail::fake();
        // Bez działającego Reverba każda próba broadcastu czeka na timeout.
        config(["broadcasting.default" => "null"]);
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $component = Livewire::actingAs($sender)
            ->test(ShowMessages::class, ['user' => $receiver]);

        for ($i = 0; $i < 12; $i++) {
            $component->set('content', 'Wiadomość ' . $i)->call('sendMessage');
        }

        $this->assertSame(10, \App\Models\Message::count());
    }
}
