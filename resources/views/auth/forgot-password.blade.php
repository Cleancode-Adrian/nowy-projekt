<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset hasła - Projekciarz.pl</title>
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
                        <i class="fa-solid fa-key text-white text-2xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset hasła</h2>
                <p class="text-gray-600">Podaj swój adres email, a wyślemy Ci link do resetowania hasła</p>
            </div>

            {{-- Success Message --}}
            @if(session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errors --}}
            @error('email')
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6" id="forgotPasswordForm">
                @csrf
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

                <button type="submit" class="w-full btn btn-primary">
                    📧 Wyślij link resetujący
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
        
        if (typeof grecaptcha !== 'undefined') {
            grecaptcha.ready(function() {
                const form = document.getElementById('forgotPasswordForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        grecaptcha.execute(siteKey, {action: 'forgot_password'}).then(function(token) {
                            const tokenInput = document.getElementById('g-recaptcha-response');
                            if (tokenInput) {
                                tokenInput.value = token;
                            }
                            form.submit();
                        }).catch(function(error) {
                            console.error('reCAPTCHA error:', error);
                            form.submit(); // Fallback - submit anyway
                        });
                    });
                }
            });
        } else {
            console.warn('reCAPTCHA not loaded');
        }
    </script>
    @endif
</body>
</html>

