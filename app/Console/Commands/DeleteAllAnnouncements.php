<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;

class DeleteAllAnnouncements extends Command
{
    protected $signature = 'announcements:delete-all {--force : Force deletion without confirmation}';
    protected $description = 'Delete all announcements from the database';

    public function handle()
    {
        $count = Announcement::count();

        if ($count === 0) {
            $this->info('Brak ogłoszeń do usunięcia.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Czy na pewno chcesz usunąć wszystkie {$count} ogłoszeń?")) {
                $this->info('Operacja anulowana.');
                return 0;
            }
        }

        $this->info("Usuwanie {$count} ogłoszeń...");

        // Usuwamy wszystkie ogłoszenia (soft delete)
        $deleted = Announcement::query()->delete();

        $this->info("Usunięto {$deleted} ogłoszeń.");

        return 0;
    }
}

