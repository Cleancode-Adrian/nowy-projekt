<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class HomePage extends Component
{
    public function render()
    {
        // Cache dla performance
        $categories = Cache::remember('home.categories', 3600, function () {
            return Category::select('id', 'name', 'slug', 'icon', 'color')
                ->withCount(['announcements' => function ($query) {
                    $query->where('status', 'published')->where('is_approved', true);
                }])
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });

        $featuredAnnouncements = Cache::remember('home.featured', 600, function () {
            return Announcement::select([
                    'id', 'user_id', 'category_id', 'title', 'description',
                    'budget_min', 'budget_max', 'budget_currency', 'location',
                    'is_urgent', 'created_at'
                ])
                ->with([
                    'user:id,name,avatar,is_verified',
                    'category:id,name,color',
                    'tags:id,name'
                ])
                ->published()
                ->where('is_urgent', true)
                ->latest()
                ->take(6)
                ->get();
        });

        $stats = Cache::remember('home.stats', 1800, function () {
            return [
                'announcements' => Announcement::published()->count(),
                'freelancers' => \App\Models\User::where('role', 'freelancer')->where('is_approved', true)->count(),
                'categories' => Category::where('is_active', true)->count(),
            ];
        });

        return view('livewire.home-page', [
            'categories' => $categories,
            'featuredAnnouncements' => $featuredAnnouncements,
            'stats' => $stats,
        ])->layout('layouts.app', [
            'title' => 'Projekciarz.pl - Znajdź najlepszego freelancera dla swojego projektu',
            'description' => 'Platforma łącząca klientów z zweryfikowanymi freelancerami. Publikuj zlecenia i otrzymuj oferty od najlepszych specjalistów w branży.',
        ]);
    }
}

