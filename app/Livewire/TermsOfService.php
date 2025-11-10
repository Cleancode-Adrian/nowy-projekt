<?php

namespace App\Livewire;

use Livewire\Component;

class TermsOfService extends Component
{
    public function render()
    {
        return view('livewire.terms-of-service')->layout('layouts.app', [
            'title' => 'Regulamin serwisu - WebFreelance',
            'description' => 'Regulamin korzystania z platformy WebFreelance.',
        ]);
    }
}
