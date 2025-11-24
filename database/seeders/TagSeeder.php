<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'HTML/CSS', 'JavaScript', 'React', 'Vue.js', 'PHP', 'Laravel',
            'WordPress', 'MySQL', 'Responsive Design', 'SEO', 'UI/UX',
            'Figma', 'Tailwind CSS', 'Bootstrap',
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                [
                    'name' => $tag,
                    'type' => 'announcement',
                ],
                [
                    'slug' => Str::slug($tag),
                    'type' => 'announcement',
                ]
            );
        }
    }
}

