<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        // Test client
        User::create([
            'name' => 'Anna Kowalska',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'is_approved' => true,
            'phone' => '+48 123 456 789',
            'company' => 'Kancelaria Prawna',
            'email_verified_at' => now(),
        ]);

        // Test freelancer (pending approval)
        User::create([
            'name' => 'Jan Nowak',
            'email' => 'jan@example.com',
            'password' => Hash::make('password'),
            'role' => 'freelancer',
            'is_approved' => false,
            'bio' => 'Frontend developer z 3-letnim doświadczeniem',
            'email_verified_at' => now(),
        ]);

        // Verified freelancer (full profile)
        User::create([
            'name' => 'Jan Kowalski',
            'email' => 'freelancer@example.com',
            'password' => Hash::make('password'),
            'role' => 'freelancer',
            'is_approved' => true,
            'is_verified' => true,
            'email_verified_at' => now(),
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
        ]);
    }
}

