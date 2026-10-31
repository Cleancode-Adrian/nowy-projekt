<?php

namespace Tests\Feature;

use App\Livewire\ProfileEdit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_freelancer_wysyla_wniosek_o_weryfikacje(): void
    {
        Storage::fake('local');

        $freelancer = User::factory()->freelancer()->create();

        Livewire::actingAs($freelancer)
            ->test(ProfileEdit::class)
            ->set('verificationDocument', UploadedFile::fake()->image('dowod.jpg'))
            ->call('submitVerification');

        $freelancer->refresh();

        $this->assertSame('pending', $freelancer->verification_status);
        $this->assertNotNull($freelancer->verification_document);
        Storage::disk('local')->assertExists($freelancer->verification_document);
    }

    public function test_klient_nie_moze_wyslac_wniosku(): void
    {
        Storage::fake('local');

        $client = User::factory()->create(['role' => 'client']);

        Livewire::actingAs($client)
            ->test(ProfileEdit::class)
            ->set('verificationDocument', UploadedFile::fake()->image('dowod.jpg'))
            ->call('submitVerification');

        $client->refresh();
        $this->assertSame('none', $client->verification_status);
    }

    public function test_admin_zatwierdza_wniosek(): void
    {
        $admin = User::factory()->admin()->create();
        $freelancer = User::factory()->freelancer()->create([
            'verification_status' => 'pending',
            'verification_document' => 'verification/dowod.jpg',
        ]);

        $this->actingAs($admin)
            ->post("/admin/verifications/{$freelancer->id}/approve")
            ->assertRedirect();

        $freelancer->refresh();
        $this->assertTrue((bool) $freelancer->is_verified);
        $this->assertSame('approved', $freelancer->verification_status);
    }

    public function test_admin_odrzuca_wniosek_z_powodem(): void
    {
        $admin = User::factory()->admin()->create();
        $freelancer = User::factory()->freelancer()->create([
            'verification_status' => 'pending',
            'verification_document' => 'verification/dowod.jpg',
        ]);

        $this->actingAs($admin)
            ->post("/admin/verifications/{$freelancer->id}/reject", ['note' => 'Zdjęcie nieczytelne'])
            ->assertRedirect();

        $freelancer->refresh();
        $this->assertFalse((bool) $freelancer->is_verified);
        $this->assertSame('rejected', $freelancer->verification_status);
        $this->assertSame('Zdjęcie nieczytelne', $freelancer->verification_note);
    }

    public function test_klient_nie_wchodzi_do_panelu_weryfikacji(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)->get('/admin/verifications')->assertForbidden();
    }

    public function test_zwykly_uzytkownik_nie_pobierze_dokumentu_weryfikacyjnego(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $freelancer = User::factory()->freelancer()->create([
            'verification_document' => 'verification/dowod.jpg',
        ]);

        $this->actingAs($client)
            ->get("/admin/verifications/{$freelancer->id}/document")
            ->assertForbidden();
    }
}
