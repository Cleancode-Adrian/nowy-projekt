<?php

namespace Tests\Feature;

use App\Livewire\AnnouncementProposals;
use App\Livewire\ContractShow;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContractsTest extends TestCase
{
    use RefreshDatabase;

    private function makeAnnouncementWithProposal(): array
    {
        $client = User::factory()->create(['role' => 'client']);
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::create(['name' => 'Testowa', 'slug' => 'testowa']);

        $announcement = Announcement::create([
            'user_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Testowe ogłoszenie',
            'description' => 'Opis',
            'budget_min' => 1000,
            'budget_max' => 2000,
            'budget_currency' => 'PLN',
            'status' => 'published',
            'is_approved' => true,
        ]);

        $proposal = Proposal::create([
            'announcement_id' => $announcement->id,
            'user_id' => $freelancer->id,
            'price' => 1500,
            'currency' => 'PLN',
            'delivery_days' => 7,
            'description' => 'Oferta testowa',
            'status' => 'pending',
        ]);

        return compact('client', 'freelancer', 'announcement', 'proposal');
    }

    public function test_akceptacja_oferty_tworzy_kontrakt_z_kamieniem_milowym(): void
    {
        ['client' => $client, 'freelancer' => $freelancer, 'announcement' => $announcement, 'proposal' => $proposal] = $this->makeAnnouncementWithProposal();

        Livewire::actingAs($client)
            ->test(AnnouncementProposals::class, ['announcement' => $announcement])
            ->call('accept', $proposal->id);

        $this->assertDatabaseHas('contracts', [
            'announcement_id' => $announcement->id,
            'proposal_id' => $proposal->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'active',
        ]);

        $contract = Contract::where('proposal_id', $proposal->id)->firstOrFail();
        $this->assertSame(1, $contract->milestones()->count());
    }

    public function test_osoba_trzecia_nie_widzi_kontraktu(): void
    {
        ['announcement' => $announcement, 'proposal' => $proposal, 'client' => $client] = $this->makeAnnouncementWithProposal();

        Livewire::actingAs($client)
            ->test(AnnouncementProposals::class, ['announcement' => $announcement])
            ->call('accept', $proposal->id);

        $contract = Contract::where('proposal_id', $proposal->id)->firstOrFail();
        $intruz = User::factory()->create(['role' => 'client']);

        $this->actingAs($intruz)->get("/kontrakty/{$contract->id}")->assertForbidden();
    }

    public function test_pelny_przeplyw_kamienia_milowego_konczy_kontrakt(): void
    {
        ['client' => $client, 'freelancer' => $freelancer, 'announcement' => $announcement, 'proposal' => $proposal] = $this->makeAnnouncementWithProposal();

        Livewire::actingAs($client)
            ->test(AnnouncementProposals::class, ['announcement' => $announcement])
            ->call('accept', $proposal->id);

        $contract = Contract::where('proposal_id', $proposal->id)->firstOrFail();
        $milestone = $contract->milestones()->first();

        // Klient nie może sam sobie zatwierdzić etapu ani go rozpocząć jako wykonawca.
        Livewire::actingAs($client)
            ->test(ContractShow::class, ['contract' => $contract])
            ->call('submitMilestone', $milestone->id);

        $this->assertDatabaseHas('contract_milestones', ['id' => $milestone->id, 'status' => 'pending']);

        Livewire::actingAs($freelancer)
            ->test(ContractShow::class, ['contract' => $contract])
            ->call('submitMilestone', $milestone->id);

        $this->assertDatabaseHas('contract_milestones', ['id' => $milestone->id, 'status' => 'submitted']);

        Livewire::actingAs($client)
            ->test(ContractShow::class, ['contract' => $contract])
            ->call('approveMilestone', $milestone->id);

        $this->assertDatabaseHas('contract_milestones', ['id' => $milestone->id, 'status' => 'approved']);
        $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'completed']);
    }
}
