<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Strona firmowa',
                'slug' => 'strona-firmowa',
                'description' => 'Profesjonalne strony dla firm i przedsiębiorstw',
                'icon' => 'fa-solid fa-building',
                'color' => '#3B82F6',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Sklepy internetowe i platformy sprzedażowe',
                'icon' => 'fa-solid fa-cart-shopping',
                'color' => '#10B981',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'WordPress',
                'slug' => 'wordpress',
                'description' => 'Strony i blogi na WordPressie',
                'icon' => 'fa-brands fa-wordpress',
                'color' => '#8B5CF6',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Aplikacja web',
                'slug' => 'aplikacja-web',
                'description' => 'Zaawansowane aplikacje webowe',
                'icon' => 'fa-solid fa-laptop-code',
                'color' => '#F59E0B',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Landing page',
                'slug' => 'landing-page',
                'description' => 'Strony docelowe i kampanie marketingowe',
                'icon' => 'fa-solid fa-rocket',
                'color' => '#EF4444',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Redesign',
                'slug' => 'redesign',
                'description' => 'Odświeżenie istniejącej strony',
                'icon' => 'fa-solid fa-paintbrush',
                'color' => '#06B6D4',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'description' => 'Projektowanie interfejsów użytkownika',
                'icon' => 'fa-solid fa-palette',
                'color' => '#EC4899',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'SEO',
                'slug' => 'seo',
                'description' => 'Optymalizacja pod wyszukiwarki',
                'icon' => 'fa-solid fa-chart-line',
                'color' => '#84CC16',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Utrzymanie i aktualizacje strony',
                'icon' => 'fa-solid fa-wrench',
                'color' => '#6366F1',
                'is_active' => true,
                'order' => 9,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $category['slug']],
                array_merge($category, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
