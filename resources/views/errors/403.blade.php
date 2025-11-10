<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Brak dostępu | Projekciarz.pl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('components.header')

    <div class="min-h-screen flex items-center justify-center px-4 py-16">
        <div class="max-w-2xl w-full text-center">
            
            <div class="mb-8">
                <div class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600">
                    403
                </div>
            </div>

            <div class="text-6xl mb-6">🔒</div>

            <h1 class="text-4xl font-bold text-gray-900 mb-4">Brak dostępu</h1>
            <p class="text-xl text-gray-600 mb-8">
                Nie masz uprawnień do przeglądania tej strony. Ta sekcja jest dostępna tylko dla administratorów lub właścicieli treści.
            </p>

            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('home') }}" 
                   class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg">
                    🏠 Strona główna
                </a>
                @guest
                    <a href="{{ route('login') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                        🔑 Zaloguj się
                    </a>
                @endguest
            </div>

        </div>
    </div>

    @include('components.footer')
</body>
</html>

