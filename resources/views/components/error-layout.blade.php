@props(['code', 'title', 'heading', 'message'])

<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>{{ $code }} - {{ $title }} | Projekciarz.pl</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-gray-50 antialiased">
    @include('components.header')

    <main class="flex flex-1 items-center">
        <div class="container-page py-16 lg:py-24">
            <div class="mx-auto max-w-xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Błąd {{ $code }}</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ $heading }}</h1>
                <p class="mt-4 text-lg leading-relaxed text-ink-500">{{ $message }}</p>

                <div class="mt-8">
                    {{ $slot }}
                </div>

                <p class="mt-10 text-sm text-ink-400">
                    Potrzebujesz pomocy? Zobacz <a href="{{ route('faq') }}" class="font-medium text-brand-600 hover:underline">FAQ</a>
                    lub napisz na <a href="mailto:biuro@cleancodeas.pl" class="font-medium text-brand-600 hover:underline">biuro@cleancodeas.pl</a>.
                </p>
            </div>
        </div>
    </main>

    @include('components.footer')
</body>
</html>
