<x-auth-layout title="Logowanie" heading="Zaloguj się do konta" subheading="Witaj ponownie. Podaj e-mail i hasło, aby kontynuować.">

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ session('success') }}
        </div>
    @endif

    @error('email')
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ $message }}</div>
    @enderror
    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
        @csrf

        <div>
            <label for="email" class="label">Adres e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   @class(['input', 'input-error' => $errors->has('email')]) placeholder="jan@firma.pl">
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="label">Hasło</label>
                <a href="{{ route('password.request') }}" class="mb-1.5 text-sm font-medium text-brand-600 hover:text-brand-700">Nie pamiętasz?</a>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input" placeholder="••••••••">
        </div>

        <label class="flex items-center gap-2.5 text-sm text-ink-600">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
            Zapamiętaj mnie na tym urządzeniu
        </label>

        <button type="submit" class="btn btn-primary w-full py-3">Zaloguj się</button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-500">
        Nie masz jeszcze konta?
        <a href="{{ route('register', \App\Http\Middleware\CaptureAttribution::linkParams(request())) }}" class="font-semibold text-brand-600 hover:text-brand-700">Załóż je bezpłatnie</a>
    </p>

    <x-slot:scripts>
    </x-slot:scripts>
</x-auth-layout>
