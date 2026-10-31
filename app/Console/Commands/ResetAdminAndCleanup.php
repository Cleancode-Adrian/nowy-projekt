<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetAdminAndCleanup extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * Hasło celowo NIE jest opcją wiersza poleceń - trafiałoby do historii
     * powłoki i listy procesów (ps). Zawsze pytamy o nie interaktywnie.
     */
    protected $signature = 'admin:reset {--email=} {--name=} {--force : Pomiń potwierdzenie (także na produkcji)}';

    /**
     * The console command description.
     */
    protected $description = 'Resetuje dane administratora i usuwa wszystkich innych użytkowników';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $others = User::where('role', '!=', 'admin')->count();

        $this->warn("⚠️  Ta komenda USUNIE wszystkich użytkowników poza jednym administratorem ({$others} kont innych niż admin).");

        // Na produkcji wymaga --force albo potwierdzenia, lokalnie pyta zawsze (poniżej).
        if (!$this->confirmToProceed('Usunięcie wszystkich użytkowników')) {
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('Na pewno kontynuować?', false)) {
            $this->info('Operacja anulowana.');
            return 1;
        }

        $this->info('🔧 Resetowanie administratora i czyszczenie użytkowników...');
        $this->newLine();

        // Pobierz dane z opcji lub zapytaj
        $email = $this->option('email') ?: $this->ask('📧 Podaj nowy email administratora');
        $password = $this->secret('🔒 Podaj nowe hasło administratora');
        $name = $this->option('name') ?: $this->ask('👤 Podaj imię/nazwę administratora', 'Administrator');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 12) {
            $this->error('Nieprawidłowy e-mail albo hasło krótsze niż 12 znaków.');
            return 1;
        }

        // Znajdź obecnego administratora lub utwórz nowego
        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            $this->info("✅ Znaleziono administratora: {$admin->email}");
            $this->info("🔄 Aktualizuję dane...");

            $admin->update([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_approved' => true,
            ]);
        } else {
            $this->info("⚠️  Nie znaleziono administratora, tworzę nowego...");

            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_approved' => true,
            ]);
        }

        // email_verified_at nie jest w $fillable - ustawiamy jawnie.
        $admin->forceFill(['email_verified_at' => now()])->save();

        $this->newLine();
        $this->info('✅ Administrator zaktualizowany:');
        $this->line("   📧 Email: {$admin->email}");
        $this->line("   👤 Nazwa: {$admin->name}");
        $this->line("   🔒 Hasło: ***********");

        $this->newLine();
        $this->info('🗑️  Usuwam wszystkich pozostałych użytkowników...');

        // Usuń wszystkich użytkowników oprócz admina
        $deletedCount = User::where('id', '!=', $admin->id)->delete();

        $this->newLine();
        $this->info("✅ Usunięto {$deletedCount} użytkowników");
        $this->info('✅ Pozostał tylko administrator');

        $this->newLine();
        $this->info('🎉 Gotowe! Możesz się teraz zalogować jako:');
        $this->line("   📧 Email: {$admin->email}");
        $this->line("   🔒 Hasło: (podane wyżej)");

        return 0;
    }
}
