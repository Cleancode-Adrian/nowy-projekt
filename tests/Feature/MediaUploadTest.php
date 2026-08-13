<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regresja: biblioteka mediów przyjmowała dowolny plik do 5 MB, więc wgranie
 * pliku .php do publicznego storage kończyło się wykonaniem kodu na serwerze.
 */
class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_odrzuca_plik_php(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/media', [
                'files' => [UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;')],
            ])
            ->assertSessionHasErrors('files.0');

        Storage::disk('public')->assertMissing('shell.php');
    }

    public function test_odrzuca_podwojne_rozszerzenie(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/media', [
                'files' => [UploadedFile::fake()->createWithContent('obrazek.php.png', 'GIF89a')],
            ])
            ->assertSessionHasErrors('files.0');
    }

    public function test_odrzuca_svg(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';

        $this->actingAs($this->admin())
            ->post('/admin/media', [
                'files' => [UploadedFile::fake()->createWithContent('ikona.svg', $svg)],
            ])
            ->assertSessionHasErrors('files.0');
    }

    public function test_odrzuca_htaccess(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/media', [
                'files' => [UploadedFile::fake()->createWithContent('.htaccess', 'AddType application/x-httpd-php .png')],
            ])
            ->assertSessionHasErrors('files.0');
    }

    public function test_przyjmuje_zwykly_obrazek(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/media', [
                'files' => [UploadedFile::fake()->image('zdjecie.jpg', 100, 100)],
            ])
            ->assertSessionHasNoErrors();

        $this->assertCount(1, Storage::disk('public')->files());
    }

    public function test_zwykly_uzytkownik_nie_wgrywa_plikow(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->post('/admin/media', [
                'files' => [UploadedFile::fake()->image('zdjecie.jpg')],
            ])
            ->assertForbidden();
    }
}
