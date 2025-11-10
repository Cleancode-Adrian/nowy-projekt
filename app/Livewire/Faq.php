<?php

namespace App\Livewire;

use Livewire\Component;

class Faq extends Component
{
    public $faqs = [];

    public function mount()
    {
        $this->faqs = [
            [
                'category' => 'Dla Klientów',
                'questions' => [
                    ['q' => 'Jak dodać ogłoszenie?', 'a' => 'Zaloguj się, przejdź do Panelu i kliknij "Dodaj ogłoszenie". Wypełnij formularz z opisem projektu, budżetem i terminem.'],
                    ['q' => 'Ile kosztuje dodanie ogłoszenia?', 'a' => 'Dodawanie ogłoszeń jest całkowicie darmowe!'],
                    ['q' => 'Jak wybrać najlepszego freelancera?', 'a' => 'Sprawdź oceny, portfolio, doświadczenie i porównaj propozycje. Możesz też napisać wiadomość przed podjęciem decyzji.'],
                ]
            ],
            [
                'category' => 'Dla Freelancerów',
                'questions' => [
                    ['q' => 'Jak wysłać ofertę?', 'a' => 'Znajdź interesujący projekt, kliknij na niego i wypełnij formularz z ceną, terminem i opisem swojej oferty.'],
                    ['q' => 'Czy mogę wycofać ofertę?', 'a' => 'Tak, możesz wycofać ofertę w panelu "Moje oferty" jeśli ma status "Oczekuje".'],
                    ['q' => 'Jak zbudować dobre portfolio?', 'a' => 'Dodaj swoje najlepsze projekty w sekcji Portfolio. Każdy projekt powinien mieć opis, zdjęcie i listę użytych technologii.'],
                ]
            ],
            [
                'category' => 'Bezpieczeństwo',
                'questions' => [
                    ['q' => 'Czy moje dane są bezpieczne?', 'a' => 'Tak! Używamy szyfrowania SSL i nie udostępniamy danych osobowych. Płatności obsługiwane są przez bezpieczne bramki.'],
                    ['q' => 'Co zrobić w przypadku oszustwa?', 'a' => 'Użyj przycisku "Zgłoś" przy treści. Sprawdzimy zgłoszenie w ciągu 24h.'],
                ]
            ],
        ];
    }

    public function render()
    {
        return view('livewire.faq')->layout('layouts.app', [
            'title' => 'FAQ - Najczęściej zadawane pytania - WebFreelance',
        ]);
    }
}

