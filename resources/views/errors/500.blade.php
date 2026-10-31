<x-error-layout code="500" title="Błąd serwera" heading="Coś poszło nie tak po naszej stronie"
                message="Wystąpił nieoczekiwany błąd. Zostaliśmy o nim powiadomieni. Odśwież stronę lub spróbuj ponownie za chwilę.">

    <div class="flex flex-wrap justify-center gap-2">
        <a href="{{ url()->current() }}" class="btn btn-primary">Odśwież stronę</a>
        <a href="{{ route('home') }}" class="btn btn-secondary">Strona główna</a>
    </div>
</x-error-layout>
