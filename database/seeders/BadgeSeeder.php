<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'slug' => 'top-freelancer',
                'name' => 'Top Freelancer',
                'description' => 'Ukończył 20+ projektów z oceną 4.5+',
                'icon' => '🏆',
                'color' => '#f59e0b',
                'requirement_value' => 20,
                'requirement_type' => 'projects',
            ],
            [
                'slug' => 'verified-pro',
                'name' => 'Verified Pro',
                'description' => 'Zweryfikowany Senior z LinkedIn',
                'icon' => '✓',
                'color' => '#3b82f6',
                'requirement_value' => null,
                'requirement_type' => 'verification',
            ],
            [
                'slug' => 'rising-star',
                'name' => 'Rising Star',
                'description' => 'Nowy talent - 10+ projektów w 3 miesiące',
                'icon' => '⭐',
                'color' => '#eab308',
                'requirement_value' => 10,
                'requirement_type' => 'quick_start',
            ],
            [
                'slug' => 'highly-rated',
                'name' => 'Highly Rated',
                'description' => 'Ocena 4.8+ z minimum 10 opinii',
                'icon' => '💎',
                'color' => '#8b5cf6',
                'requirement_value' => 48,
                'requirement_type' => 'rating',
            ],
            [
                'slug' => 'trusted',
                'name' => 'Trusted',
                'description' => 'Zweryfikowany z 5+ ukończonymi projektami',
                'icon' => '🛡️',
                'color' => '#10b981',
                'requirement_value' => 5,
                'requirement_type' => 'trusted',
            ],
            [
                'slug' => 'expert',
                'name' => 'Expert',
                'description' => '50+ projektów, Senior, 4.7+ rating',
                'icon' => '👨‍💼',
                'color' => '#dc2626',
                'requirement_value' => 50,
                'requirement_type' => 'expert',
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}

