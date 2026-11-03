<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityAudit extends Command
{
    protected $signature = 'security:audit
                            {--fix : Usuń konta demonstracyjne i zresetuj hasła ze znanych wycieków}';

    protected $description = 'Sprawdza konta administratorów i konta demonstracyjne o publicznie znanych hasłach';

    /**
     * Hasła, które trafiły do repozytorium - każde konto z takim hasłem
     * traktujemy jak przejęte.
     */
    private const LEAKED_PASSWORDS = [
        'password',
        'Projekciarz_123!!!',
    ];

    private const DEMO_EMAILS = [
        'client@example.com',
        'jan@example.com',
        'freelancer@example.com',
        'admin@example.com',
    ];

    public function handle(): int
    {
        $fix = (bool) $this->option('fix');
        $problems = 0;

        $this->info('== Konta z rolą admin ==');
        $admins = User::withTrashed()->where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('Brak kont administratora - panel /admin jest niedostępny dla nikogo.');
        }

        foreach ($admins as $admin) {
            $status = $admin->trashed() ? 'usunięty' : ($admin->is_approved ? 'aktywny' : 'niezatwierdzony');
            $this->line(sprintf('  #%d  %s  [%s]  ostatnia zmiana: %s',
                $admin->id, $admin->email, $status, $admin->updated_at?->format('Y-m-d') ?? '-'));
        }

        $this->newLine();
        $this->info('== Konta z wyciekniętym hasłem ==');

        foreach (User::withTrashed()->get() as $user) {
            foreach (self::LEAKED_PASSWORDS as $leaked) {
                if (!Hash::check($leaked, (string) $user->password)) {
                    continue;
                }

                $problems++;
                $this->error("  #{$user->id}  {$user->email}  (rola: {$user->role})");

                if ($fix) {
                    $user->password = Hash::make(Str::password(24));
                    $user->save();
                    $this->line('    -> hasło zresetowane na losowe, wymagany reset przez /forgot-password');
                }
                break;
            }
        }

        if ($problems === 0) {
            $this->line('  Brak.');
        }

        $this->newLine();
        $this->info('== Konta demonstracyjne ==');
        $demo = User::withTrashed()->whereIn('email', self::DEMO_EMAILS)->get();

        if ($demo->isEmpty()) {
            $this->line('  Brak.');
        }

        foreach ($demo as $user) {
            $problems++;
            $this->warn("  #{$user->id}  {$user->email}");

            if ($fix) {
                $user->forceDelete();
                $this->line('    -> usunięte');
            }
        }

        $this->newLine();
        $this->info('== Konfiguracja ==');
        $this->checkConfig('APP_DEBUG wyłączony', !config('app.debug'));
        $this->checkConfig('APP_ENV = production', config('app.env') === 'production');
        $this->checkConfig('Bezpieczne ciasteczka sesji', (bool) config('session.secure'));
        $this->checkConfig('APP_KEY ustawiony', (bool) config('app.key'));

        if ($problems > 0 && !$fix) {
            $this->newLine();
            $this->warn('Uruchom ponownie z --fix, aby usunąć konta demo i zresetować wyciekłe hasła.');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function checkConfig(string $label, bool $ok): void
    {
        $this->line(($ok ? '  [OK]   ' : '  [UWAGA]') . " {$label}");
    }
}
