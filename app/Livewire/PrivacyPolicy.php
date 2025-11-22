<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class PrivacyPolicy extends Component
{
    public function render()
    {
        $page = Page::where(function($query) {
                $query->where('slug', 'polityka-prywatnosci')
                      ->orWhere(function($q) {
                          $q->where('is_system', true)
                            ->where('title', 'like', '%polityka%');
                      });
            })
            ->active()
            ->first();

        if (!$page) {
            abort(404, 'Strona nie została znaleziona');
        }

        return view('livewire.page-show', [
            'page' => $page,
        ])->layout('layouts.app', [
            'title' => ($page->meta_title ?: $page->title) . ' - Projekciarz.pl',
            'description' => $page->meta_description ?: 'Polityka prywatności platformy Projekciarz.pl.',
        ]);
    }
}
