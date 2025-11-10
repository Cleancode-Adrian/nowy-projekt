<?php

namespace App\Livewire;

use Livewire\Component;

class PrivacyPolicy extends Component
{
    public function render()
    {
        return view('livewire.privacy-policy')->layout('layouts.app', [
            'title' => 'Polityka prywatności - Projekciarz.pl',
            'description' => 'Polityka prywatności platformy Projekciarz.pl.',
        ]);
    }
}
