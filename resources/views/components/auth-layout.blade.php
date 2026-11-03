@props(['title', 'heading', 'subheading' => null])

<!DOCTYPE html>
<html lang="pl" class="h-full bg-white">
<head>
    <x-gtm-head />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>{{ $title }} - Projekciarz.pl</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-white">
<x-gtm-body />
<div class="flex min-h-full">
    {{-- Formularz --}}
    <div class="flex flex-1 flex-col justify-center px-6 py-12 sm:px-10 lg:flex-none lg:px-20 xl:px-24">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <a href="{{ route('home') }}" class="inline-block rounded-md" aria-label="Projekciarz.pl - strona główna">
                <x-logo />
            </a>

            <h1 class="mt-10 text-2xl font-bold tracking-tight text-ink-900">{{ $heading }}</h1>
            @if($subheading)
                <p class="mt-2 text-sm text-ink-500">{{ $subheading }}</p>
            @endif

            <div class="mt-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Panel boczny --}}
    <div class="relative isolate hidden flex-1 overflow-hidden bg-ink-950 lg:flex lg:flex-col lg:justify-between lg:p-14">
        <div class="bg-grid absolute inset-0 -z-10 [mask-image:radial-gradient(ellipse_at_center,black_20%,transparent_70%)]"></div>
        <div class="absolute -bottom-32 -right-32 -z-10 h-[30rem] w-[30rem] rounded-full bg-brand-600/30 blur-3xl"></div>

        <p class="text-sm font-medium text-ink-400">Platforma zleceń dla stron, sklepów i aplikacji</p>

        <div class="max-w-md">
            <p class="text-3xl font-semibold leading-snug tracking-tight text-white">
                Jedno zlecenie, kilka ofert, wszystko w jednym miejscu.
            </p>
            <ul class="mt-8 space-y-4 text-ink-300">
                @foreach([
                    'Publikacja zlecenia jest bezpłatna i do niczego nie zobowiązuje',
                    'Konta wykonawców zatwierdza administrator',
                    'Oceny wystawiają tylko klienci po realizacji projektu',
                ] as $point)
                    <li class="flex gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-xs text-ink-500">&copy; {{ date('Y') }} Projekciarz.pl</p>
    </div>
</div>

{{ $scripts ?? '' }}
<script>
    // Powrót przyciskiem "Wstecz" pokazuje stronę z pamięci przeglądarki ze starym
    // tokenem CSRF -> wysłanie formularza kończyło się 419. Przeładowujemy ją.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { window.location.reload(); }
    });
</script>
</body>
</html>
