<x-error-layout code="403" title="Brak dostępu" heading="Nie masz dostępu do tej strony"
                message="Ta sekcja jest dostępna tylko dla uprawnionych użytkowników - właściciela treści lub administratora.">

    <div class="flex flex-wrap justify-center gap-2">
        <a href="{{ route('home') }}" class="btn btn-primary">Strona główna</a>
        @guest
            <a href="{{ route('login') }}" class="btn btn-secondary">Zaloguj się</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Mój panel</a>
        @endguest
    </div>
</x-error-layout>
