<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Strona nie znaleziona | WebFreelance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('components.header')

    <div class="min-h-screen flex items-center justify-center px-4 py-16">
        <div class="max-w-2xl w-full text-center">
            
            {{-- Animated 404 --}}
            <div class="mb-8 relative">
                <div class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 animate-pulse">
                    404
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-6xl animate-bounce">🔍</div>
                </div>
            </div>

            {{-- Message --}}
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Ups! Strona nie istnieje</h1>
            <p class="text-xl text-gray-600 mb-8">
                Nie mogliśmy znaleźć strony, której szukasz. Może została przeniesiona lub usunięta?
            </p>

            {{-- Search --}}
            <div class="max-w-lg mx-auto mb-8">
                <form action="{{ route('search.advanced') }}" method="GET" class="relative">
                    <input type="text" 
                           name="q" 
                           placeholder="Czego szukasz? Spróbuj wyszukać..." 
                           class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-14">
                    <button type="submit" 
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all shadow-lg">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('home') }}" 
                   class="bg-white hover:bg-gray-50 border-2 border-gray-200 hover:border-blue-400 rounded-xl p-6 transition-all hover:shadow-lg group">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🏠</div>
                    <h3 class="font-bold text-gray-900 mb-1">Strona główna</h3>
                    <p class="text-sm text-gray-600">Wróć na start</p>
                </a>

                <a href="{{ route('announcements.index') }}" 
                   class="bg-white hover:bg-gray-50 border-2 border-gray-200 hover:border-purple-400 rounded-xl p-6 transition-all hover:shadow-lg group">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">📋</div>
                    <h3 class="font-bold text-gray-900 mb-1">Ogłoszenia</h3>
                    <p class="text-sm text-gray-600">Przeglądaj projekty</p>
                </a>

                <a href="{{ route('leaderboard') }}" 
                   class="bg-white hover:bg-gray-50 border-2 border-gray-200 hover:border-green-400 rounded-xl p-6 transition-all hover:shadow-lg group">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">🏆</div>
                    <h3 class="font-bold text-gray-900 mb-1">Ranking</h3>
                    <p class="text-sm text-gray-600">Top freelancerzy</p>
                </a>
            </div>

            {{-- Help Text --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-left">
                <h3 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    Potrzebujesz pomocy?
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li>• Sprawdź czy URL jest poprawny</li>
                    <li>• Użyj wyszukiwarki powyżej</li>
                    <li>• Zobacz <a href="{{ route('faq') }}" class="underline font-semibold hover:text-blue-600">FAQ</a> z najczęstszymi pytaniami</li>
                    <li>• Skontaktuj się z nami: <a href="mailto:kontakt@webfreelance.pl" class="underline font-semibold hover:text-blue-600">kontakt@webfreelance.pl</a></li>
                </ul>
            </div>

        </div>
    </div>

    @include('components.footer')

    <style>
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .animate-bounce {
            animation: bounce 2s infinite;
        }
    </style>
</body>
</html>

