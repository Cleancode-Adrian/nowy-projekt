<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - Projekciarz.pl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
    @endif
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen py-12">

    <div class="w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-user-plus text-white text-2xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Zarejestruj się</h2>
                <p class="text-gray-600">Utwórz darmowe konto i zacznij publikować ogłoszenia</p>
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

            <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm">
                @csrf
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imię i nazwisko *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input" placeholder="Jan Kowalski">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="input" placeholder="jan@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="input" placeholder="+48 123 456 789">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Firma/Organizacja</label>
                    <input type="text" name="company" value="{{ old('company') }}" class="input" placeholder="Nazwa firmy">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasło *</label>
                    <input type="password" name="password" required minlength="8" class="input" placeholder="Minimum 8 znaków">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Potwierdź hasło *</label>
                    <input type="password" name="password_confirmation" required minlength="8" class="input" placeholder="Powtórz hasło">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zarejestruj się jako *</label>
                    <select name="role" required class="input">
                        <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Klient (szukam freelancera)</option>
                        <option value="freelancer" {{ old('role') === 'freelancer' ? 'selected' : '' }}>Freelancer (oferuję usługi)</option>
                    </select>
                </div>

                {{-- Checkboxes --}}
                <div class="space-y-3 pt-2">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="accept_privacy" value="1" required class="w-4 h-4 text-blue-600 border-gray-300 rounded mt-1">
                        <span class="ml-3 text-sm text-gray-700">
                            Akceptuję <a href="{{ route('privacy-policy') }}" target="_blank" class="text-blue-600 hover:text-blue-700 underline font-medium">politykę prywatności</a> i wyrażam zgodę na przetwarzanie moich danych osobowych *
                        </span>
                    </label>

                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="accept_terms" value="1" required class="w-4 h-4 text-blue-600 border-gray-300 rounded mt-1">
                        <span class="ml-3 text-sm text-gray-700">
                            Akceptuję <a href="{{ route('terms-of-service') }}" target="_blank" class="text-blue-600 hover:text-blue-700 underline font-medium">regulamin serwisu</a> *
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full btn btn-primary mt-6">
                    <i class="fa-solid fa-user-plus mr-2"></i>
                    Zarejestruj się
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Masz już konto? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Zaloguj się</a>
                </p>
            </div>
        </div>
    </div>

    @if(config('services.recaptcha.site_key'))
    <script>
        const siteKey = '{{ config("services.recaptcha.site_key") }}';

        const form = document.getElementById('registerForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (typeof grecaptcha === 'undefined') {
                    e.preventDefault();
                    alert('Nie udało się załadować reCAPTCHA. Wyłącz blokowanie skryptów (np. AdBlock) i spróbuj ponownie.');
                    return;
                }

                e.preventDefault();
                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: 'register' })
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

