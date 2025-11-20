<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Utwórz przykładowych użytkowników (klientów) jeśli nie istnieją
        $users = [];
        
        // Użytkownik 1
        $user1 = User::firstOrCreate(
            ['email' => 'anna.kowalska@example.com'],
            [
                'name' => 'Anna Kowalska',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_approved' => true,
                'phone' => '+48 501 234 567',
                'company' => 'Kancelaria Prawna Kowalska',
                'email_verified_at' => now()->subDays(30),
            ]
        );
        $users[] = $user1;

        // Użytkownik 2
        $user2 = User::firstOrCreate(
            ['email' => 'piotr.nowak@example.com'],
            [
                'name' => 'Piotr Nowak',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_approved' => true,
                'phone' => '+48 502 345 678',
                'company' => 'Nowak Sp. z o.o.',
                'email_verified_at' => now()->subDays(20),
            ]
        );
        $users[] = $user2;

        // Użytkownik 3
        $user3 = User::firstOrCreate(
            ['email' => 'magdalena.wisniewska@example.com'],
            [
                'name' => 'Magdalena Wiśniewska',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_approved' => true,
                'phone' => '+48 503 456 789',
                'company' => 'Wisniewska Design Studio',
                'email_verified_at' => now()->subDays(15),
            ]
        );
        $users[] = $user3;

        // Użytkownik 4
        $user4 = User::firstOrCreate(
            ['email' => 'tomasz.wojcik@example.com'],
            [
                'name' => 'Tomasz Wójcik',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_approved' => true,
                'phone' => '+48 504 567 890',
                'company' => 'Wójcik Tech Solutions',
                'email_verified_at' => now()->subDays(10),
            ]
        );
        $users[] = $user4;

        // Użytkownik 5
        $user5 = User::firstOrCreate(
            ['email' => 'katarzyna.lewandowska@example.com'],
            [
                'name' => 'Katarzyna Lewandowska',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_approved' => true,
                'phone' => '+48 505 678 901',
                'company' => 'Lewandowska Marketing',
                'email_verified_at' => now()->subDays(5),
            ]
        );
        $users[] = $user5;

        // Pobierz kategorie
        $ecommerceCategory = Category::where('slug', 'e-commerce')->first();
        $wordpressCategory = Category::where('slug', 'wordpress')->first();
        $webAppCategory = Category::where('slug', 'aplikacja-web')->first();
        $uiuxCategory = Category::where('slug', 'ui-ux-design')->first();
        $seoCategory = Category::where('slug', 'seo')->first();

        // Pobierz tagi
        $phpTag = Tag::where('name', 'PHP')->first();
        $laravelTag = Tag::where('name', 'Laravel')->first();
        $wordpressTag = Tag::where('name', 'WordPress')->first();
        $javascriptTag = Tag::where('name', 'JavaScript')->first();
        $uiuxTag = Tag::where('name', 'UI/UX')->first();
        $seoTag = Tag::where('name', 'SEO')->first();

        // Ogłoszenie 1: E-commerce
        $announcement1 = Announcement::updateOrCreate(
            ['title' => 'Sklep internetowy WooCommerce - integracja płatności'],
            [
                'user_id' => $users[0]->id,
                'category_id' => $ecommerceCategory?->id,
                'title' => 'Sklep internetowy WooCommerce - integracja płatności',
                'description' => 'Szukam doświadczonego developera do stworzenia sklepu internetowego na WooCommerce. Projekt obejmuje:

• Instalację i konfigurację WooCommerce
• Integrację z systemem płatności (Przelewy24, PayPal)
• Konfigurację dostaw i kosztów wysyłki
• Responsywny design zgodny z brandem
• Optymalizację wydajności

Wymagania:
- Doświadczenie z WooCommerce (minimum 2 lata)
- Znajomość PHP i WordPress
- Portfolio z podobnymi projektami

Projekt do realizacji w ciągu 4-6 tygodni. Oferuję elastyczne terminy i możliwość długoterminowej współpracy przy utrzymaniu sklepu.',
                'budget_min' => 5000,
                'budget_max' => 8000,
                'budget_currency' => 'PLN',
                'deadline' => now()->addWeeks(6),
                'location' => 'Zdalna',
                'status' => 'published',
                'is_approved' => true,
                'is_urgent' => false,
                'approved_at' => now()->subDays(2),
                'views_count' => 24,
                'proposals_count' => 3,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]
        );

        if ($wordpressTag) $announcement1->tags()->syncWithoutDetaching([$wordpressTag->id]);
        if ($phpTag) $announcement1->tags()->syncWithoutDetaching([$phpTag->id]);

        // Ogłoszenie 2: WordPress
        $announcement2 = Announcement::updateOrCreate(
            ['title' => 'Strona firmowa WordPress - redesign istniejącej strony'],
            [
                'user_id' => $users[1]->id,
                'category_id' => $wordpressCategory?->id,
                'title' => 'Strona firmowa WordPress - redesign istniejącej strony',
                'description' => 'Potrzebuję kompleksowego redesignu mojej strony firmowej na WordPress. Obecna strona jest przestarzała i nie działa dobrze na mobile.

Zakres prac:
• Analiza obecnej strony i benchmark konkurencji
• Projektowanie nowego layoutu w Figma
• Implementacja na WordPress (custom theme lub page builder)
• Optymalizacja pod SEO
• Integracja z formularzami kontaktowymi
• Responsywny design (mobile-first)

Szukam freelancera z doświadczeniem w:
- WordPress development
- UI/UX design
- SEO

Budżet: 3000-5000 PLN. Termin: 3-4 tygodnie.',
                'budget_min' => 3000,
                'budget_max' => 5000,
                'budget_currency' => 'PLN',
                'deadline' => now()->addWeeks(4),
                'location' => 'Warszawa / Zdalna',
                'status' => 'published',
                'is_approved' => true,
                'is_urgent' => false,
                'approved_at' => now()->subDays(1),
                'views_count' => 18,
                'proposals_count' => 5,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]
        );

        if ($wordpressTag) $announcement2->tags()->syncWithoutDetaching([$wordpressTag->id]);
        if ($uiuxTag) $announcement2->tags()->syncWithoutDetaching([$uiuxTag->id]);
        if ($seoTag) $announcement2->tags()->syncWithoutDetaching([$seoTag->id]);

        // Ogłoszenie 3: Aplikacja web
        $announcement3 = Announcement::updateOrCreate(
            ['title' => 'Aplikacja web Laravel - system zarządzania projektami'],
            [
                'user_id' => $users[2]->id,
                'category_id' => $webAppCategory?->id,
                'title' => 'Aplikacja web Laravel - system zarządzania projektami',
                'description' => 'Szukam doświadczonego developera Laravel do stworzenia systemu zarządzania projektami dla małej agencji.

Funkcjonalności:
• Panel klienta i panel administracyjny
• Zarządzanie projektami, zadaniami i terminami
• System powiadomień email
• Generowanie raportów PDF
• Integracja z kalendarzem
• Dashboard z statystykami

Wymagania techniczne:
- Laravel 10+
- MySQL
- Vue.js lub Livewire (do uzgodnienia)
- API REST

Budżet: 15000-25000 PLN. Projekt podzielony na etapy. Szukam długoterminowej współpracy.',
                'budget_min' => 15000,
                'budget_max' => 25000,
                'budget_currency' => 'PLN',
                'deadline' => now()->addMonths(3),
                'location' => 'Zdalna',
                'status' => 'published',
                'is_approved' => true,
                'is_urgent' => true,
                'approved_at' => now()->subHours(12),
                'views_count' => 45,
                'proposals_count' => 8,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ]
        );

        if ($laravelTag) $announcement3->tags()->syncWithoutDetaching([$laravelTag->id]);
        if ($phpTag) $announcement3->tags()->syncWithoutDetaching([$phpTag->id]);
        if ($javascriptTag) $announcement3->tags()->syncWithoutDetaching([$javascriptTag->id]);

        // Ogłoszenie 4: UI/UX Design
        $announcement4 = Announcement::updateOrCreate(
            ['title' => 'Projekt UI/UX dla aplikacji mobilnej - fintech'],
            [
                'user_id' => $users[3]->id,
                'category_id' => $uiuxCategory?->id,
                'title' => 'Projekt UI/UX dla aplikacji mobilnej - fintech',
                'description' => 'Szukam doświadczonego UI/UX designera do projektu aplikacji mobilnej w branży fintech.

Zakres prac:
• Research użytkowników i konkurencji
• User journey mapping
• Wireframy i prototypy w Figma
• Design system (kolory, typografia, komponenty)
• Mockupy ekranów aplikacji
• Prototyp interaktywny

Wymagania:
- Portfolio z projektami aplikacji mobilnych
- Doświadczenie w fintech (mile widziane)
- Znajomość Figma
- Zrozumienie zasad UX

Projekt do realizacji w ciągu 6-8 tygodni. Możliwość współpracy przy implementacji.',
                'budget_min' => 8000,
                'budget_max' => 12000,
                'budget_currency' => 'PLN',
                'deadline' => now()->addWeeks(8),
                'location' => 'Kraków / Zdalna',
                'status' => 'published',
                'is_approved' => true,
                'is_urgent' => false,
                'approved_at' => now()->subDays(3),
                'views_count' => 32,
                'proposals_count' => 6,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]
        );

        if ($uiuxTag) $announcement4->tags()->syncWithoutDetaching([$uiuxTag->id]);

        // Ogłoszenie 5: SEO
        $announcement5 = Announcement::updateOrCreate(
            ['title' => 'Optymalizacja SEO - sklep e-commerce'],
            [
                'user_id' => $users[4]->id,
                'category_id' => $seoCategory?->id,
                'title' => 'Optymalizacja SEO - sklep e-commerce',
                'description' => 'Szukam specjalisty SEO do kompleksowej optymalizacji sklepu internetowego.

Zakres prac:
• Audyt SEO obecnej strony
• Optymalizacja on-page (meta tagi, nagłówki, alt teksty)
• Struktura URL i internal linking
• Optymalizacja szybkości ładowania
• Schema markup dla produktów
• Raport z rekomendacjami

Wymagania:
- Minimum 3 lata doświadczenia w SEO
- Portfolio z case studies (wzrost ruchu organicznego)
- Znajomość Google Search Console, Analytics
- Doświadczenie z e-commerce

Budżet: 4000-6000 PLN. Szukam długoterminowej współpracy przy utrzymaniu pozycji.',
                'budget_min' => 4000,
                'budget_max' => 6000,
                'budget_currency' => 'PLN',
                'deadline' => now()->addWeeks(4),
                'location' => 'Zdalna',
                'status' => 'published',
                'is_approved' => true,
                'is_urgent' => false,
                'approved_at' => now()->subDays(5),
                'views_count' => 28,
                'proposals_count' => 4,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]
        );

        if ($seoTag) $announcement5->tags()->syncWithoutDetaching([$seoTag->id]);

        $this->command->info('✅ Dodano 5 przykładowych ogłoszeń!');
    }
}

