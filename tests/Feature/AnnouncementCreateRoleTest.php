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
 * Zlecenia dodają tylko zleceniodawcy (i admin). Wykonawca składa oferty.
 */
class AnnouncementCreateRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);
    }

    public function test_wykonawca_nie_wejdzie_na_formularz_zlecenia(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'is_approved' => true]);

        $this->actingAs($freelancer)
            ->get(route('announcements.create'))
            ->assertRedirect(route('announcements.index'));

        $this->actingAs($freelancer)
            ->get(route('announcements.index'))
            ->assertSee('Zlecenia mogą dodawać tylko zleceniodawcy');
    }

    public function test_wykonawca_nie_zapisze_zlecenia_z_pominieciem_formularza(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'is_approved' => true]);
        $category = Category::first();

        // Bezpośrednie wywołanie akcji Livewire (mount przekierowuje, więc
        // testujemy samo submit na komponencie z ustawionymi polami).
        $component = new CreateAnnouncement();
        $this->actingAs($freelancer);

        try {
            $component->submit();
            $this->fail('Wykonawca zapisał zlecenie');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        $this->assertDatabaseCount('announcements', 0);
    }

    public function test_zleceniodawca_moze_dodac_zlecenie(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);

        Livewire::actingAs($client)
            ->test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', Category::first()->id)
            ->call('submit')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('announcements', ['user_id' => $client->id, 'status' => 'pending']);
    }

    public function test_api_odrzuca_wykonawce(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'is_approved' => true]);

        $this->actingAs($freelancer, 'sanctum')
            ->postJson('/api/announcements', [
                'category_id' => Category::first()->id,
                'title' => 'Zlecenie od wykonawcy',
                'description' => 'Opis',
            ])
            ->assertStatus(403);
    }

    public function test_wykonawca_nie_widzi_linku_dodaj_zlecenie_w_stopce(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'is_approved' => true]);

        $html = $this->actingAs($freelancer)->get(route('faq'))->getContent();
        $footer = substr($html, strpos($html, '<footer'));

        $this->assertStringNotContainsString(route('announcements.create'), $footer);
    }
}
