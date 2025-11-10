<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Błąd serwera | Projekciarz.pl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('components.header')

    <div class="min-h-screen flex items-center justify-center px-4 py-16">
        <div class="max-w-2xl w-full text-center">
            
            <div class="mb-8">
                <div class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 via-orange-600 to-yellow-600">
                    500
                </div>
            </div>

            <div class="text-6xl mb-6">😵</div>

            <h1 class="text-4xl font-bold text-gray-900 mb-4">Ups! Coś poszło nie tak</h1>
            <p class="text-xl text-gray-600 mb-8">
                Przepraszamy, wystąpił błąd serwera. Nasz zespół został powiadomiony i już pracuje nad rozwiązaniem.
            </p>

            <div class="flex items-center justify-center gap-4 mb-8">
                <a href="{{ route('home') }}" 
                   class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl">
                    🏠 Strona główna
                </a>
                <button onclick="window.history.back()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                    ← Wróć
                </button>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-left">
                <h3 class="font-bold text-yellow-900 mb-3">💡 Co możesz zrobić?</h3>
                <ul class="space-y-2 text-sm text-yellow-800">
                    <li>• Odśwież stronę (F5)</li>
                    <li>• Spróbuj ponownie za chwilę</li>
                    <li>• Wyczyść cache przeglądarki</li>
                    <li>• Jeśli problem się powtarza, napisz do nas: <a href="mailto:kontakt@Projekciarz.pl.pl" class="underline font-semibold">kontakt@Projekciarz.pl.pl</a></li>
                </ul>
            </div>

        </div>
    </div>

    @include('components.footer')
</body>
</html>

