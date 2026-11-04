<?php

namespace Tests\Feature;

use App\Livewire\CreateAnnouncement;
use App\Livewire\ProposalsList;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Proposal;
use App\Models\User;
use App\Support\Ga4MeasurementProtocol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Lejek leada: jeden Lead na zlecenie, qualify_lead po akceptacji admina
 * (GA4 Measurement Protocol), close_convert_lead przy wyborze oferty,
 * autozapis szkicu i zwinięte pola opcjonalne.
 */
class LeadQualityTrackingTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->category = Category::create(['name' => 'Strony WWW', 'slug' => 'strony-www', 'is_active' => true]);
    }

    private function publishAs(User $client, array $cookies = [])
    {
        return Livewire::withCookies($cookies)
            ->actingAs($client)
            ->test(CreateAnnouncement::class)
            ->set('title', 'Strona internetowa dla gabinetu')
            ->set('description', str_repeat('Potrzebuję prostej strony wizytówki z formularzem kontaktowym. ', 3))
            ->set('category_id', $this->category->id)
            ->call('submit');
    }

    public function test_client_id_z_ga_cookie(): void
    {
        $this->assertSame('1234567890.1700000000', Ga4MeasurementProtocol::clientIdFromCookie('GA1.1.1234567890.1700000000'));
        $this->assertNull(Ga4MeasurementProtocol::clientIdFromCookie('smieci'));
        $this->assertNull(Ga4MeasurementProtocol::clientIdFromCookie(null));
    }

    public function test_lead_raz_na_zlecenie_i_zapis_client_id(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);

        $this->publishAs($client, ['_ga' => 'GA1.1.1234567890.1700000000'])->assertRedirect(route('dashboard'));
        // Livewire::test nie kończy żądania, więc flash nie "starzeje się" - robimy to ręcznie,
        // jak po prawdziwym żądaniu wysyłającym formularz.
        session()->ageFlashData();

        $announcement = Announcement::firstOrFail();
        $this->assertNotNull($announcement->lead_tracked_at);
        $this->assertSame('1234567890.1700000000', $announcement->ga_client_id);

        $leads = collect(session('fb_events'))->where('name', 'Lead');
        $this->assertCount(1, $leads);
        $this->assertSame('lead-' . $announcement->id, $leads->first()['id']);

        // Strona sukcesu: Lead jest, a po odświeżeniu już go nie ma.
        $this->actingAs($client)->get(route('dashboard'))
            ->assertSee("{eventID: \"lead-{$announcement->id}\"}", false)
            ->assertSee("localStorage.removeItem('projekciarz_draft')", false);
        $this->actingAs($client)->get(route('dashboard'))
            ->assertDontSee("{eventID: \"lead-{$announcement->id}\"}", false);
    }

    public function test_qualify_lead_po_akceptacji_admina_tylko_raz(): void
    {
        config(['services.ga4.api_secret' => 'sekret-testowy', 'services.ga4.measurement_id' => 'G-XY55LLSQMQ']);
        Http::fake(['www.google-analytics.com/*' => Http::response('', 204)]);

        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $announcement = Announcement::create([
            'user_id' => $client->id,
            'category_id' => $this->category->id,
            'title' => 'Strona dla gabinetu',
            'description' => 'Opis',
            'status' => 'pending',
            'is_approved' => false,
            'ga_client_id' => '1234567890.1700000000',
        ]);

        $this->actingAs($admin)->post(route('admin.announcements.approve', $announcement->id))->assertRedirect();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'mp/collect?measurement_id=G-XY55LLSQMQ&api_secret=sekret-testowy')
                && $request['client_id'] === '1234567890.1700000000'
                && $request['events'][0]['name'] === 'qualify_lead'
                && $request['events'][0]['params']['lead_type'] === 'zlecenie'
                && $request['events'][0]['params']['item_category'] === 'strony-www';
        });
        $firstSentAt = $announcement->fresh()->qualify_tracked_at;
        $this->assertNotNull($firstSentAt);
        Http::assertSentCount(1);

        // Ponowne zatwierdzenie (np. po edycji) nie wysyła drugiego zdarzenia -
        // trackQualifiedLead() przestawiłby qualify_tracked_at na nowy czas.
        // (Liczby żądań nie da się tu sprawdzić: w teście kolejne żądanie dzieli
        // instancję aplikacji i ponownie odpala zadania "po odpowiedzi" z poprzedniego.)
        $this->travel(2)->hours();
        $announcement->fresh()->update(['is_approved' => false]);
        $this->actingAs($admin)->post(route('admin.announcements.approve', $announcement->id));
        $this->assertTrue($firstSentAt->equalTo($announcement->fresh()->qualify_tracked_at));
    }

    public function test_bez_zgody_na_statystyke_nie_ma_client_id_i_nic_nie_wychodzi(): void
    {
        config(['services.ga4.api_secret' => 'sekret-testowy']);
        Http::fake();

        $this->assertFalse(Ga4MeasurementProtocol::send(null, 'qualify_lead'));
        Http::assertNothingSent();
    }

    public function test_close_convert_lead_przy_akceptacji_oferty(): void
    {
        $client = User::factory()->create(['role' => 'client', 'is_approved' => true]);
        $freelancer = User::factory()->create(['role' => 'freelancer', 'is_approved' => true]);
        $announcement = Announcement::create([
            'user_id' => $client->id,
            'category_id' => $this->category->id,
            'title' => 'Strona dla gabinetu',
            'description' => 'Opis',
            'status' => 'published',
            'is_approved' => true,
        ]);
        $proposal = Proposal::create([
            'announcement_id' => $announcement->id,
            'user_id' => $freelancer->id,
            'price' => 4500,
            'currency' => 'PLN',
            'delivery_days' => 14,
            'description' => str_repeat('Oferta wykonania strony. ', 5),
            'status' => 'pending',
        ]);

        $component = Livewire::actingAs($client)
            ->test(ProposalsList::class, ['announcementId' => $announcement->id])
            ->call('accept', $proposal->id);

        // Kilka zdarzeń dl-event w jednej odpowiedzi - assertDispatched patrzy tylko na pierwsze.
        $payloads = collect($component->effects['dispatches'] ?? [])
            ->where('name', 'dl-event')
            ->pluck('params.payload');

        $this->assertTrue($payloads->contains(fn ($p) => $p['event'] === 'accept_proposal'));
        $this->assertTrue($payloads->contains(fn ($p) => $p == ['event' => 'close_convert_lead', 'lead_type' => 'zlecenie', 'value' => 4500.0, 'currency' => 'PLN']));
    }

    public function test_pola_opcjonalne_zwiniete_i_autozapis_szkicu(): void
    {
        $html = $this->get(route('announcements.create'))->assertOk()->getContent();

        $this->assertStringContainsString('Więcej szczegółów', $html);
        $this->assertMatchesRegularExpression('/<details class="group rounded-xl border border-ink-100"\s*>/', $html, 'Sekcja opcjonalna powinna być domyślnie zwinięta');
        $this->assertStringContainsString('Strona firmowa dla piekarni, 5 podstron, formularz kontaktowy, mam logo', $html);
        // @script Livewire trafia do strony w danych komponentu, nie jako zwykły <script>.
        $this->assertStringContainsString('projekciarz_draft', $html);
    }
}
