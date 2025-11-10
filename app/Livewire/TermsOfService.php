<?php

namespace App\Livewire;

use Livewire\Component;

class TermsOfService extends Component
{
    public function render()
    {
        return view('livewire.terms-of-service')->layout('layouts.app', [
            'title' => 'Regulamin serwisu - Projekciarz.pl',
            'description' => 'Regulamin korzystania z platformy Projekciarz.pl.',
        ]);
    }
}
