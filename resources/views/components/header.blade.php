@php
    // Strony CMS w menu - jedno zapytanie dla wersji desktop i mobile.
    $headerPages = \App\Models\Page::inMenu('header')->with('children')->get();

    $navItems = [
        ['label' => 'Zlecenia', 'route' => 'announcements.index', 'active' => 'announcements.index'],
        ['label' => 'Szukaj', 'route' => 'search.advanced', 'active' => 'search.advanced'],
        ['label' => 'Ranking wykonawców', 'route' => 'leaderboard', 'active' => 'leaderboard'],
        ['label' => 'Poradnik', 'route' => 'blog.index', 'active' => 'blog.*'],
    ];

    if (auth()->check()) {
        $user = auth()->user();
        $unreadMessages = \App\Models\Message::where('receiver_id', $user->id)->where('is_read', false)->count();
        $unreadNotifications = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
        $extraBadge = 0;

        if ($user->isClient()) {
            // Nowe oferty do ogłoszeń zleceniodawcy
            $extraBadge = \App\Models\Proposal::whereHas('announcement', fn ($q) => $q->where('user_id', $user->id))
                ->where('status', 'pending')
                ->count();
        } elseif ($user->isFreelancer()) {
            // Dopasowania zapisanych wyszukiwań są kosztowne (pętla po wyszukiwaniach),
            // a nagłówek renderuje się na każdej stronie - stąd krótki cache per użytkownik.
            $extraBadge = \Illuminate\Support\Facades\Cache::remember("header.matches.{$user->id}", 300, function () use ($user) {
                $count = 0;
                foreach ($user->savedSearches()->where('notify_on_match', true)->get() as $search) {
                    $matching = $search->findMatchingAnnouncements();
                    if ($search->last_notified_at) {
                        $matching = $matching->filter(fn ($a) => $a->created_at > $search->last_notified_at);
                    }
                    $count += $matching->count();
                }
                return $count;
            });
        }

        $totalBadge = $unreadNotifications + $extraBadge;
        $initial = mb_strtoupper(mb_substr($user->name, 0, 1));
    }
@endphp

<header class="sticky top-0 z-40 border-b border-ink-100 bg-white/85 backdrop-blur-md supports-[backdrop-filter]:bg-white/75"
        x-data="{ userMenuOpen: false, mobileMenuOpen: false }"
        @keydown.escape.window="userMenuOpen = false; mobileMenuOpen = false">
    <div class="container-page">
        <div class="flex h-16 items-center justify-between gap-4">

            {{-- Logo + nawigacja --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="rounded-md" aria-label="Projekciarz.pl - strona główna">
                    <x-logo />
                </a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Główna nawigacja">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           @class(['nav-link', 'nav-link-active' => request()->routeIs($item['active'])])
                           @if(request()->routeIs($item['active'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    @foreach($headerPages as $page)
                        <x-menu-item :page="$page" />
                    @endforeach
                </nav>
            </div>

            {{-- Akcje --}}
            <div class="flex items-center gap-1 sm:gap-2">
                @auth
                    @if($user->isClient())
                        <a href="{{ route('announcements.create') }}" class="btn btn-primary hidden lg:inline-flex">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                            Dodaj zlecenie
                        </a>
                    @elseif($user->isFreelancer())
                        <a href="{{ route('proposals.index') }}" @class(['nav-link hidden lg:inline-flex', 'nav-link-active' => request()->routeIs('proposals.*')])>
                            Moje oferty
                        </a>
                    @endif

                    {{-- Wiadomości --}}
                    <a href="{{ route('messages.index') }}"
                       class="relative rounded-lg p-2 text-ink-500 transition-colors hover:bg-ink-50 hover:text-ink-900"
                       x-data="{ count: {{ (int) $unreadMessages }} }"
                       x-init="window.liveNotifications?.addEventListener('new_message', () => count++)"
                       :aria-label="'Wiadomości' + (count ? ', nieprzeczytane: ' + count : '')"
                       aria-label="Wiadomości">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        <span x-show="count > 0" x-cloak x-text="count > 9 ? '9+' : count"
                              class="absolute right-0.5 top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white"></span>
                    </a>

                    {{-- Powiadomienia --}}
                    <a href="{{ route('notifications') }}"
                       class="relative rounded-lg p-2 text-ink-500 transition-colors hover:bg-ink-50 hover:text-ink-900"
                       x-data="{ count: {{ (int) $totalBadge }} }"
                       x-init="window.liveNotifications?.addEventListener('notification', () => count++)"
                       aria-label="Powiadomienia">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                        <span x-show="count > 0" x-cloak x-text="count > 9 ? '9+' : count"
                              class="absolute right-0.5 top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white"></span>
                    </a>

                    {{-- Menu użytkownika --}}
                    <div class="relative ml-1" @click.outside="userMenuOpen = false">
                        <button type="button" @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 rounded-full p-0.5 pr-2 transition-colors hover:bg-ink-50"
                                :aria-expanded="userMenuOpen" aria-haspopup="true" aria-label="Menu konta">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt=""
                                     class="h-8 w-8 rounded-full object-cover ring-1 ring-ink-200">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ink-900 text-sm font-semibold text-white">{{ $initial }}</span>
                            @endif
                            <svg class="hidden h-4 w-4 text-ink-400 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                        </button>

                        <div x-show="userMenuOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute right-0 mt-2 w-64 origin-top-right rounded-xl border border-ink-100 bg-white p-1.5 shadow-xl">

                            <div class="flex items-center gap-3 px-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-ink-900">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-ink-500">{{ $user->email }}</p>
                                </div>
                                <span class="badge badge-brand ml-auto shrink-0">{{ $user->isClient() ? 'Zleceniodawca' : 'Wykonawca' }}</span>
                            </div>

                            <div class="my-1 h-px bg-ink-100"></div>

                            <a href="{{ route('dashboard') }}" class="dropdown-item"><i class="fa-solid fa-gauge w-4 text-ink-400"></i>Panel</a>
                            <a href="{{ route('contracts.index') }}" class="dropdown-item"><i class="fa-solid fa-list-check w-4 text-ink-400"></i>Moje kontrakty</a>

                            @if($user->isClient())
                                <a href="{{ route('announcements.create') }}" class="dropdown-item lg:hidden"><i class="fa-solid fa-plus w-4 text-ink-400"></i>Dodaj zlecenie</a>
                            @endif

                            @if($user->isFreelancer())
                                <a href="{{ route('proposals.index') }}" class="dropdown-item"><i class="fa-solid fa-briefcase w-4 text-ink-400"></i>Moje oferty</a>
                                <a href="{{ route('portfolio.index') }}" class="dropdown-item"><i class="fa-solid fa-folder-open w-4 text-ink-400"></i>Portfolio</a>
                            @endif

                            <a href="{{ route('saved.index') }}" class="dropdown-item"><i class="fa-solid fa-bookmark w-4 text-ink-400"></i>Zapisane</a>
                            <a href="{{ route('stats') }}" class="dropdown-item"><i class="fa-solid fa-chart-line w-4 text-ink-400"></i>Statystyki</a>

                            <div class="my-1 h-px bg-ink-100"></div>

                            <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fa-solid fa-gear w-4 text-ink-400"></i>Ustawienia konta</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-red-600 hover:bg-red-50 hover:text-red-700">
                                    <i class="fa-solid fa-right-from-bracket w-4"></i>Wyloguj się
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link hidden lg:inline-flex">Zaloguj się</a>
                    <a href="{{ route('register') }}" class="btn btn-primary hidden lg:inline-flex">Załóż konto</a>
                @endauth

                {{-- Przycisk menu mobilnego --}}
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-lg p-2 text-ink-600 hover:bg-ink-50 hover:text-ink-900 lg:hidden"
                        :aria-expanded="mobileMenuOpen" aria-controls="mobile-menu" aria-label="Otwórz menu">
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu mobilne --}}
    <div id="mobile-menu" x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="border-t border-ink-100 bg-white lg:hidden">
        <nav class="container-page flex flex-col gap-1 py-4" aria-label="Nawigacja mobilna">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs($item['active'])])>{{ $item['label'] }}</a>
            @endforeach
            @foreach($headerPages as $page)
                <x-menu-item :page="$page" />
            @endforeach

            @auth
                @if($user->isClient())
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary mt-3">Dodaj zlecenie</a>
                @elseif($user->isFreelancer())
                    <a href="{{ route('proposals.index') }}" class="nav-link">Moje oferty</a>
                @endif
            @else
                <div class="mt-3 grid grid-cols-2 gap-2 border-t border-ink-100 pt-4">
                    <a href="{{ route('login') }}" class="btn btn-secondary">Zaloguj się</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Załóż konto</a>
                </div>
            @endauth
        </nav>
    </div>
</header>
