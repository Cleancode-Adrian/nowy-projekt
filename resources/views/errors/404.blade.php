<x-error-layout code="404" title="Strona nie znaleziona" heading="Tej strony tu nie ma"
                message="Adres może być nieaktualny albo strona została przeniesiona. Spróbuj wyszukać to, czego potrzebujesz.">

    <form action="{{ route('search.advanced') }}" method="GET" class="mx-auto flex max-w-md gap-2">
        <label for="q" class="sr-only">Szukaj</label>
        <input id="q" type="search" name="q" placeholder="Szukaj zleceń lub wykonawców…" class="input py-3">
        <button type="submit" class="btn btn-primary shrink-0 py-3">Szukaj</button>
    </form>

    <div class="mt-6 flex flex-wrap justify-center gap-2">
        <a href="{{ route('home') }}" class="btn btn-secondary">Strona główna</a>
        <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Zlecenia</a>
        <a href="{{ route('leaderboard') }}" class="btn btn-secondary">Ranking</a>
    </div>
</x-error-layout>
