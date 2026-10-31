<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Kopia zapasowa bazy danych - projekt wcześniej nie miał żadnej automatyzacji
 * backupów (patrz BEZPIECZENSTWO.md, sekcja "do zrobienia").
 *
 * Zapisuje zrzut do storage/app/private/backups (dysk "local" - poza webrootem,
 * niedostępny publicznie) i usuwa kopie starsze niż --keep dni.
 */
class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--keep=14 : Liczba dni, przez które przechowywane są kopie}';

    protected $description = 'Tworzy skompresowaną kopię zapasową bazy danych i usuwa stare kopie';

    public function handle(): int
    {
        $connectionName = config('database.default');
        $config = config("database.connections.{$connectionName}");

        if (!$config) {
            $this->error("Nieznane połączenie bazy danych: {$connectionName}");
            return self::FAILURE;
        }

        $timestamp = now()->format('Y-m-d_His');
        $disk = Storage::disk('local');
        $disk->makeDirectory('backups');

        $sql = match ($config['driver']) {
            'sqlite' => $this->dumpSqlite($config),
            'mysql', 'mariadb' => $this->dumpMysql($config),
            default => null,
        };

        if ($sql === null) {
            $this->error("Backup nieobsługiwany dla sterownika: {$config['driver']}");
            return self::FAILURE;
        }

        $extension = $config['driver'] === 'sqlite' ? 'sqlite.gz' : 'sql.gz';
        $filename = "backups/backup_{$timestamp}.{$extension}";
        $disk->put($filename, gzencode($sql, 9));

        $sizeKb = round(strlen($sql) / 1024, 1);
        $this->info("Zapisano kopię: storage/app/private/{$filename} ({$sizeKb} KB przed kompresją)");

        $this->cleanup($disk, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    private function dumpSqlite(array $config): ?string
    {
        $path = $config['database'];

        if (!is_file($path)) {
            $this->error("Plik bazy SQLite nie istnieje: {$path}");
            return null;
        }

        return file_get_contents($path);
    }

    private function dumpMysql(array $config): ?string
    {
        $binary = (new \Symfony\Component\Process\ExecutableFinder())->find('mysqldump', 'mysqldump');

        $process = new Process([
            $binary,
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--single-transaction',
            '--skip-lock-tables',
            '--no-tablespaces',
            $config['database'],
        ], null, [
            'MYSQL_PWD' => $config['password'],
        ]);

        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('mysqldump nie powiodło się: ' . $process->getErrorOutput());
            return null;
        }

        return $process->getOutput();
    }

    private function cleanup($disk, int $keepDays): void
    {
        $cutoff = now()->subDays($keepDays);
        $removed = 0;

        foreach ($disk->files('backups') as $file) {
            if ($disk->lastModified($file) < $cutoff->timestamp) {
                $disk->delete($file);
                $removed++;
            }
        }

        if ($removed > 0) {
            $this->info("Usunięto {$removed} kopii starszych niż {$keepDays} dni.");
        }
    }
}
