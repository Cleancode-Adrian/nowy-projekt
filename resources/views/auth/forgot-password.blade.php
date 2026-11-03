<x-auth-layout title="Reset hasła" heading="Nie pamiętasz hasła?" subheading="Podaj adres e-mail powiązany z kontem, a wyślemy link do ustawienia nowego hasła.">

    @if(session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</div>
    @endif

    @error('email')
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="forgotPasswordForm">
        @csrf

        <div>
            <label for="email" class="label">Adres e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   @class(['input', 'input-error' => $errors->has('email')]) placeholder="jan@firma.pl">
        </div>

        <button type="submit" class="btn btn-primary w-full py-3">Wyślij link</button>
    </form>

    <p class="mt-8 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">&larr; Wróć do logowania</a>
    </p>

    <x-slot:scripts>
    </x-slot:scripts>
</x-auth-layout>
