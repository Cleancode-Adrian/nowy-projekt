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
        $categories = Cache::remember('home.categories.v2', 3600, function () {
            return Category::select('id', 'name', 'slug', 'icon', 'color', 'description')
                ->withCount(['announcements' => function ($query) {
                    $query->whereIn('status', ['published', 'closed'])->where('is_approved', true);
                }])
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        });

        $featuredAnnouncements = Cache::remember('home.featured.v2', 600, function () {
            return Announcement::select([
                    'id', 'user_id', 'category_id', 'title', 'description',
                    'budget_min', 'budget_max', 'budget_currency', 'location',
                    'is_urgent', 'deadline', 'status', 'created_at'
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

        // Klucz jest wersjonowany: dodanie nowego pola do tablicy statystyk
        // przy starym kluczu wysypywało stronę główną, dopóki cache nie wygasł.
        // Zmieniasz kształt tablicy - podbij wersję.
        $stats = Cache::remember('home.stats.v2', 1800, function () {
            // Średni budżet liczony z realnych ogłoszeń. Przy małej próbce
            // nie pokazujemy go wcale - lepiej nic niż liczba wzięta z sufitu.
            $budgets = Announcement::published()
                ->whereNotNull('budget_min')
                ->whereNotNull('budget_max')
                ->selectRaw('AVG((budget_min + budget_max) / 2) as srednia, COUNT(*) as ile')
                ->first();

            return [
                'announcements' => Announcement::published()->count(),
                'freelancers' => \App\Models\User::where('role', 'freelancer')->where('is_approved', true)->count(),
                'categories' => Category::where('is_active', true)->count(),
                'avg_budget' => ($budgets && $budgets->ile >= 5) ? (int) round($budgets->srednia / 100) * 100 : null,
            ];
        });

        return view('livewire.home-page', [
            'categories' => $categories,
            'featuredAnnouncements' => $featuredAnnouncements,
            'stats' => $stats,
        ])->layout('layouts.app', [
            'title' => 'Szukasz wykonawcy strony WWW? Zbierz oferty - Projekciarz.pl',
            'description' => 'Opisz projekt i otrzymaj oferty od wykonawców stron, sklepów i aplikacji. Porównaj wyceny, portfolio i opinie. Publikacja zlecenia jest bezpłatna.',
        ]);
    }
}

