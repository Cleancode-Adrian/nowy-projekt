<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="flex items-center mb-4 hover:opacity-80 transition-opacity">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-code text-white text-2xl"></i>
                    </div>
                    <span class="ml-3 text-2xl font-bold">Projekciarz.pl</span>
                </a>
                <p class="text-gray-400 text-sm">
                    Platforma łącząca klientów z profesjonalnymi wykonawcami projektów.
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="font-semibold mb-4">Platforma</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('announcements.index') }}" class="hover:text-white transition-colors">Ogłoszenia</a></li>
                    <li><a href="{{ route('search.advanced') }}" class="hover:text-white transition-colors">Szukaj projektów</a></li>
                    <li><a href="{{ route('leaderboard') }}" class="hover:text-white transition-colors">Ranking</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h3 class="font-semibold mb-4">Informacje</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('terms-of-service') }}" class="hover:text-white transition-colors">Regulamin</a></li>
                    <li><a href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">Polityka prywatności</a></li>
                    <li><a href="mailto:biuro@cleancodeas.pl" class="hover:text-white transition-colors">Kontakt</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} Projekciarz.pl. Wszelkie prawa zastrzeżone.</p>
        </div>
    </div>
</footer>

