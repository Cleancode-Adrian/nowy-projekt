<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();

        // Konta demonstracyjne mają słabe, publicznie znane hasła - nigdy na produkcji.
        if (app()->environment('production')) {
            $this->command?->warn('Pomijam konta demonstracyjne (środowisko produkcyjne).');
            return;
        }

        $this->seedDemoAccounts();
    }

    private function seedAdmin(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@projekciarz.pl');
        $password = (string) env('ADMIN_PASSWORD', '');
        $generated = false;

        if ($password === '') {
            $password = Str::password(20);
            $generated = true;
        }

        $admin = User::withTrashed()->firstOrNew(['email' => $email]);

        $admin->fill([
            'name' => (string) env('ADMIN_NAME', 'Administrator'),
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $admin->deleted_at = null;
        $admin->email_verified_at = $admin->email_verified_at ?? now();

        // Istniejącemu adminowi nie nadpisujemy hasła bez wyraźnej decyzji w .env.
        if (!$admin->exists || env('ADMIN_PASSWORD')) {
            $admin->password = Hash::make($password);
        } else {
            $generated = false;
            $password = null;
        }

        $admin->save();

        $this->command?->info("Konto admina: {$email}");

        if ($generated) {
            $this->command?->warn("Wygenerowane hasło (zapisz teraz, nie pojawi się ponownie): {$password}");
        }
    }

    private function seedDemoAccounts(): void
    {
        $demoPassword = Hash::make('password');

        $accounts = [
            [
                'email' => 'client@example.com',
                'name' => 'Anna Kowalska',
                'role' => 'client',
                'is_approved' => true,
                'phone' => '+48 123 456 789',
                'company' => 'Kancelaria Prawna',
            ],
            [
                'email' => 'jan@example.com',
                'name' => 'Jan Nowak',
                'role' => 'freelancer',
                'is_approved' => false,
                'bio' => 'Frontend developer z 3-letnim doświadczeniem',
            ],
            [
                'email' => 'freelancer@example.com',
                'name' => 'Jan Kowalski',
                'role' => 'freelancer',
                'is_approved' => true,
                'is_verified' => true,
                'verified_at' => now()->subDays(10),
                'bio' => 'Full-stack developer z 5-letnim doświadczeniem. Specjalizuję się w Laravel, React i Tailwind CSS. Zrealizowałem ponad 50 projektów dla klientów z całej Polski.',
                'phone' => '+48 555 123 456',
                'skills' => ['Laravel', 'React', 'Tailwind CSS', 'MySQL', 'API Development', 'UI/UX Design'],
                'experience_level' => 'senior',
                'linkedin_url' => 'https://linkedin.com/in/jankowalski',
                'github_url' => 'https://github.com/jankowalski',
                'website' => 'https://jankowalski.dev',
                'average_rating' => 4.8,
                'ratings_count' => 15,
                'completed_projects' => 52,
                'profile_views' => 347,
            ],
        ];

        foreach ($accounts as $attributes) {
            $email = $attributes['email'];
            unset($attributes['email']);

            $user = User::firstOrNew(['email' => $email]);
            $user->fill($attributes);
            $user->password = $demoPassword;
            $user->email_verified_at = $user->email_verified_at ?? now();
            $user->save();
        }
    }
}
