<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use App\Models\User;

class CleanupUsers extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:cleanup {--except-admin} {--force : Pomiń potwierdzenie (także na produkcji)}';

    /**
     * The console command description.
     */
    protected $description = 'Usuwa wszystkich użytkowników oprócz administratora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Znajdź administratora
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->error('❌ Nie znaleziono konta administratora!');
            return 1;
        }

        $count = User::where('id', '!=', $admin->id)->count();
        $this->warn("⚠️  Ta komenda usunie {$count} użytkowników (zostaje tylko {$admin->email}).");

        // Na produkcji wymaga --force albo potwierdzenia; lokalnie pytamy zawsze.
        if (!$this->confirmToProceed('Usunięcie wszystkich użytkowników')) {
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('Na pewno kontynuować?', false)) {
            $this->info('Operacja anulowana.');
            return 1;
        }

        $this->info('🗑️  Usuwanie użytkowników...');
        $this->info("✅ Administrator: {$admin->name} ({$admin->email}) - zostanie zachowany");

        // Usuń wszystkich użytkowników oprócz admina
        $deletedCount = User::where('id', '!=', $admin->id)->delete();

        $this->info("✅ Usunięto {$deletedCount} użytkowników");
        $this->info("✅ Pozostał tylko administrator");

        return 0;
    }
}
