<x-auth-layout title="Rejestracja" heading="Załóż bezpłatne konto" subheading="Publikuj zlecenia albo składaj oferty - wybierz, jak chcesz korzystać z platformy.">

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="registerForm" class="group/form space-y-5">
        @csrf

        {{-- Rola --}}
        <fieldset>
            <legend class="label">Chcę</legend>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    'client' => ['Zlecić projekt', 'Szukam wykonawcy'],
                    'freelancer' => ['Realizować zlecenia', 'Oferuję usługi'],
                ] as $value => [$label, $hint])
                    <label class="relative flex cursor-pointer flex-col rounded-lg border border-ink-200 p-3.5 transition-colors hover:border-ink-300 has-checked:border-brand-500 has-checked:bg-brand-50/60 has-checked:ring-1 has-checked:ring-brand-500 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-brand-500">
                        <input type="radio" id="role-{{ $value }}" name="role" value="{{ $value }}" class="sr-only" @checked(old('role', 'client') === $value) required>
                        <span class="text-sm font-semibold text-ink-900">{{ $label }}</span>
                        <span class="mt-0.5 text-xs text-ink-500">{{ $hint }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <div>
            <label for="name" class="label">Imię i nazwisko</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                   @class(['input', 'input-error' => $errors->has('name')]) placeholder="Jan Kowalski">
        </div>

        <div>
            <label for="email" class="label">Adres e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   @class(['input', 'input-error' => $errors->has('email')]) placeholder="jan@firma.pl">
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="phone" class="label">Telefon <span class="font-normal text-ink-400">(opcjonalnie)</span></label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="input" placeholder="+48 123 456 789">
            </div>
            <div>
                <label for="company" class="label">Firma <span class="font-normal text-ink-400">(opcjonalnie)</span></label>
                <input id="company" type="text" name="company" value="{{ old('company') }}" autocomplete="organization" class="input" placeholder="Nazwa firmy">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Hasło</label>
                <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                       @class(['input', 'input-error' => $errors->has('password')]) placeholder="Min. 8 znaków">
            </div>
            <div>
                <label for="password_confirmation" class="label">Powtórz hasło</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="input">
            </div>
        </div>

        <p class="hidden group-has-[#role-freelancer:checked]/form:block rounded-lg bg-ink-50 px-3.5 py-3 text-xs leading-relaxed text-ink-600">
            Konta wykonawców zatwierdza administrator. Logowanie i składanie ofert odblokuje się
            po akceptacji - powiadomimy Cię o tym mailowo.
        </p>

        <div class="space-y-3">
            <label class="flex items-start gap-2.5 text-sm text-ink-600">
                <input type="checkbox" name="accept_privacy" value="1" required @checked(old('accept_privacy')) class="mt-0.5 h-4 w-4 shrink-0 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                <span>Akceptuję <a href="{{ route('privacy-policy') }}" target="_blank" class="font-medium text-brand-600 underline underline-offset-2">politykę prywatności</a> i wyrażam zgodę na przetwarzanie danych osobowych.</span>
            </label>
            <label class="flex items-start gap-2.5 text-sm text-ink-600">
                <input type="checkbox" name="accept_terms" value="1" required @checked(old('accept_terms')) class="mt-0.5 h-4 w-4 shrink-0 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                <span>Akceptuję <a href="{{ route('terms-of-service') }}" target="_blank" class="font-medium text-brand-600 underline underline-offset-2">regulamin serwisu</a>.</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-full py-3">Załóż konto</button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-500">
        Masz już konto?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Zaloguj się</a>
    </p>

    <x-slot:scripts>
    </x-slot:scripts>
</x-auth-layout>
