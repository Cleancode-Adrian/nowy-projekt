<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weryfikacja Email - WebFreelance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('components.header')

    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="card text-center">
                <div class="text-6xl mb-6">📧</div>

                <h1 class="text-2xl font-bold text-gray-900 mb-4">Zweryfikuj swój email</h1>

                <p class="text-gray-600 mb-6">
                    Wysłaliśmy link weryfikacyjny na adres:<br>
                    <strong class="text-gray-900">{{ auth()->user()->email }}</strong>
                </p>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-6">
                    Kliknij w link w emailu aby aktywować konto. Jeśli nie otrzymałeś emaila, sprawdź folder SPAM.
                </p>

                <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">
                        📨 Wyślij ponownie
                    </button>
                </form>

                <div class="text-sm text-gray-500">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        ← Powrót do dashboardu
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')
</body>
</html>

