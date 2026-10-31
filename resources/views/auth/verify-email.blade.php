<x-auth-layout title="Weryfikacja e-mail" heading="Potwierdź adres e-mail" subheading="To ostatni krok przed korzystaniem z pełni platformy.">

    <div class="rounded-xl border border-ink-100 bg-ink-50/60 p-5">
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
            </span>
            <div class="text-sm">
                <p class="text-ink-600">Wysłaliśmy link weryfikacyjny na adres:</p>
                <p class="mt-0.5 font-semibold text-ink-900">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <p class="mt-4 text-sm text-ink-500">Kliknij link w wiadomości, aby aktywować konto. Jeśli jej nie widzisz, sprawdź folder SPAM.</p>
    </div>

    @if(session('success'))
        <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
        @csrf
        <button type="submit" class="btn btn-primary w-full py-3">Wyślij link ponownie</button>
    </form>

    <div class="mt-8 flex items-center justify-between text-sm">
        <a href="{{ route('dashboard') }}" class="font-semibold text-brand-600 hover:text-brand-700">&larr; Przejdź do panelu</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-ink-500 hover:text-ink-800">Wyloguj się</button>
        </form>
    </div>
</x-auth-layout>
