<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CleanupUsers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:cleanup {--except-admin}';

    /**
     * The console command description.
     */
    protected $description = 'Usuwa wszystkich użytkowników oprócz administratora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️  Usuwanie użytkowników...');

        // Znajdź administratora
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->error('❌ Nie znaleziono konta administratora!');
            return 1;
        }

        $this->info("✅ Administrator: {$admin->name} ({$admin->email}) - zostanie zachowany");

        // Usuń wszystkich użytkowników oprócz admina
        $deletedCount = User::where('id', '!=', $admin->id)->delete();

        $this->info("✅ Usunięto {$deletedCount} użytkowników");
        $this->info("✅ Pozostał tylko administrator");

        return 0;
    }
}

