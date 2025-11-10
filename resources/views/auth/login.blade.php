<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Projekciarz.pl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-code text-white text-2xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Zaloguj się</h2>
                <p class="text-gray-600">Witaj ponownie! Zaloguj się do swojego konta</p>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="input"
                        placeholder="jan@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasło</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="input"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Zapamiętaj mnie</span>
                    </label>
                </div>

                <button type="submit" class="w-full btn btn-primary">
                    🔓 Zaloguj się
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Nie masz konta?{' '}
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                        Zarejestruj się
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

