<?php

namespace Database\Seeders;

use App\Models\BlogSchedule;
use Illuminate\Database\Seeder;

/**
 * Domyślna konfiguracja automatycznego generowania wpisów blogowych.
 *
 * `is_enabled` zostaje na false - admin musi świadomie włączyć automatyzację
 * (i podać klucz OpenAI w /admin/blog/generator), żeby uniknąć niezamierzonych
 * kosztów API. Lista tematów miesza ogólne poradniki dla freelancerów/klientów
 * z tematami wprost o samej platformie, żeby wpisy realnie ją promowały.
 */
class BlogScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            'Jak działa Projekciarz.pl - kompletny przewodnik krok po kroku',
            'Jak dodać pierwsze ogłoszenie na Projekciarz.pl i otrzymać oferty w 24 godziny',
            'Ranking wykonawców na Projekciarz.pl - jak zdobyć wysoką pozycję i więcej zleceń',
            'Jak wybrać najlepszego wykonawcę spośród ofert na Projekciarz.pl',
            'System ocen i opinii na Projekciarz.pl - dlaczego warto sprawdzać reputację wykonawcy',
            'Dlaczego warto zamieścić ogłoszenie zamiast szukać wykonawcy na własną rękę',
            'Jak znaleźć pierwszych klientów jako freelancer w Polsce',
            'Ile kosztuje strona internetowa w 2025 roku - przewodnik po cenach',
            'Jak napisać ogłoszenie o projekt, które przyciągnie najlepszych wykonawców',
            'Freelancer czy agencja - co wybrać dla swojego projektu',
            'Najczęstsze błędy przy zlecaniu strony internetowej',
            'Jak ustalać stawki jako freelancer w branży IT',
            'Portfolio freelancera - jak zbudować je krok po kroku i zdobywać więcej klientów',
            'Bezpieczna współpraca zdalna - jak zabezpieczyć się przy zleceniach online',
            'Jak rozpoznać wiarygodnego wykonawcę online',
            'WordPress czy aplikacja webowa - co lepiej sprawdzi się w Twoim projekcie',
            'Jak napisać skuteczną ofertę na zlecenie i wygrać z konkurencją',
        ];

        BlogSchedule::query()->firstOrCreate([], [
            'is_enabled' => false,
            'time' => '09:00',
            'frequency' => 'every_3_days',
            'count' => 1,
            'topics' => implode("\n", $topics),
            'category_id' => null,
            'tags' => null,
            'download_image' => true,
            'image_source' => 'unsplash',
            'auto_publish' => false,
        ]);
    }
}
