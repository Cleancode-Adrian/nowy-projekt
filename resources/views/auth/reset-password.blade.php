<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustaw nowe hasło - Projekciarz.pl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
    @endif
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-lock text-white text-2xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Ustaw nowe hasło</h2>
                <p class="text-gray-600">Wprowadź nowe hasło dla swojego konta</p>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6" id="resetPasswordForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="input"
                        placeholder="jan@example.com"
                        autofocus>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nowe hasło</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="input"
                        placeholder="••••••••"
                        minlength="8">
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 znaków</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Potwierdź hasło</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="input"
                        placeholder="••••••••">
                </div>

                <button type="submit" class="w-full btn btn-primary">
                    🔒 Zresetuj hasło
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                    ← Powrót do logowania
                </a>
            </div>
        </div>
    </div>

    @if(config('services.recaptcha.site_key'))
    <script>
        const siteKey = '{{ config("services.recaptcha.site_key") }}';

        const form = document.getElementById('resetPasswordForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (typeof grecaptcha === 'undefined') {
                    e.preventDefault();
                    alert('Nie udało się załadować reCAPTCHA. Wyłącz blokowanie skryptów (np. AdBlock) i spróbuj ponownie.');
                    return;
                }

                e.preventDefault();
                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: 'reset_password' })
                        .then(function (token) {
                            const tokenInput = document.getElementById('g-recaptcha-response');
                            if (tokenInput) {
                                tokenInput.value = token;
                            }
                            form.submit();
                        })
                        .catch(function (error) {
                            console.error('reCAPTCHA error:', error);
                            alert('Weryfikacja reCAPTCHA nie powiodła się. Odśwież stronę i spróbuj ponownie.');
                        });
                });
            });
        }
    </script>
    @endif
</body>
</html>

