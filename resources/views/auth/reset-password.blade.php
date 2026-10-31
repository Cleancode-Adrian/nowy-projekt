<x-auth-layout title="Nowe hasło" heading="Ustaw nowe hasło" subheading="Hasło musi mieć co najmniej 8 znaków.">

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5" id="resetPasswordForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        <div>
            <label for="email" class="label">Adres e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus autocomplete="email"
                   @class(['input', 'input-error' => $errors->has('email')]) placeholder="jan@firma.pl">
        </div>

        <div>
            <label for="password" class="label">Nowe hasło</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                   @class(['input', 'input-error' => $errors->has('password')]) placeholder="Min. 8 znaków">
        </div>

        <div>
            <label for="password_confirmation" class="label">Powtórz hasło</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
        </div>

        <button type="submit" class="btn btn-primary w-full py-3">Zapisz nowe hasło</button>
    </form>

    <p class="mt-8 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">&larr; Wróć do logowania</a>
    </p>

    <x-slot:scripts>
        @include('auth.partials.recaptcha', ['formId' => 'resetPasswordForm', 'action' => 'reset_password'])
    </x-slot:scripts>
</x-auth-layout>
