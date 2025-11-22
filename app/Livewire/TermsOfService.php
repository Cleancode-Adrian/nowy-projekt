<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class TermsOfService extends Component
{
    public function render()
    {
        $page = Page::where(function($query) {
                $query->where('slug', 'regulamin')
                      ->orWhere(function($q) {
                          $q->where('is_system', true)
                            ->where('title', 'like', '%regulamin%');
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
            'description' => $page->meta_description ?: 'Regulamin korzystania z platformy Projekciarz.pl.',
        ]);
    }
}
