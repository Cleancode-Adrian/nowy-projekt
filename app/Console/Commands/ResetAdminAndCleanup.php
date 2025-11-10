<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetAdminAndCleanup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:reset {--email=} {--password=} {--name=}';

    /**
     * The console command description.
     */
    protected $description = 'Resetuje dane administratora i usuwa wszystkich innych użytkowników';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Resetowanie administratora i czyszczenie użytkowników...');
        $this->newLine();

        // Pobierz dane z opcji lub zapytaj
        $email = $this->option('email') ?: $this->ask('📧 Podaj nowy email administratora');
        $password = $this->option('password') ?: $this->secret('🔒 Podaj nowe hasło administratora');
        $name = $this->option('name') ?: $this->ask('👤 Podaj imię/nazwę administratora', 'Administrator');

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
                'email_verified_at' => now(),
            ]);
        } else {
            $this->info("⚠️  Nie znaleziono administratora, tworzę nowego...");

            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_approved' => true,
                'email_verified_at' => now(),
            ]);
        }

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

