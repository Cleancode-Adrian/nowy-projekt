<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50" x-data="{ userMenuOpen: false, mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo --}}
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <div class="w-9 h-9 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-code text-white text-base"></i>
                        </div>
                        <span class="ml-2 text-lg sm:text-xl font-bold text-gray-900">Projekciarz.pl</span>
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <nav class="hidden md:ml-10 lg:flex md:space-x-6">
                    <a href="{{ route('announcements.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Ogłoszenia
                    </a>
                    <a href="{{ route('leaderboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Ranking
                    </a>
                    @php
                        $headerPages = \App\Models\Page::inMenu('header')->with('children')->get();
                    @endphp
                    @foreach($headerPages as $page)
                        <x-menu-item :page="$page" />
                    @endforeach
                </nav>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    aria-label="Toggle menu">
                <i class="fa-solid fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                <i class="fa-solid fa-times text-xl" x-show="mobileMenuOpen" style="display: none;"></i>
            </button>

            {{-- User Menu --}}
            <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                @auth
                    {{-- Quick Actions (role-based) --}}
                    @if(auth()->user()->isClient())
                        <a href="{{ route('announcements.create') }}"
                           class="hidden lg:flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            Dodaj ogłoszenie
                        </a>
                    @elseif(auth()->user()->isFreelancer())
                        <a href="{{ route('proposals.index') }}"
                           class="hidden lg:flex items-center gap-2 text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fa-solid fa-briefcase"></i>
                            Moje oferty
                        </a>
                    @endif

                    {{-- Messages Icon --}}
                    <a href="{{ route('messages.index') }}" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fa-solid fa-envelope text-base sm:text-lg md:text-xl"></i>
                        @php
                            $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadMessages > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                {{ $unreadMessages > 9 ? '9+' : $unreadMessages }}
                            </span>
                        @endif
                    </a>

                    {{-- Notifications Icon --}}
                    <a href="{{ route('notifications') }}" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fa-solid fa-bell text-base sm:text-lg md:text-xl"></i>
                        @php
                            $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                            </span>
                        @endif
                    </a>

                    {{-- User Dropdown --}}
                    <div class="relative" @click.away="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="w-9 h-9 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="w-9 h-9 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold border-2 border-gray-200">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <i class="fa-solid fa-chevron-down text-xs hidden sm:block"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="userMenuOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2"
                             style="display: none;">

                            {{-- User Info --}}
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                    {{ auth()->user()->isClient() ? '👤 Klient' : '💼 Freelancer' }}
                                </span>
                            </div>

                            {{-- Menu Items --}}
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-gauge w-5"></i>
                                Dashboard
                            </a>

                            @if(auth()->user()->isClient())
                                <a href="{{ route('announcements.create') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 lg:hidden">
                                    <i class="fa-solid fa-plus w-5"></i>
                                    Dodaj ogłoszenie
                                </a>
                            @endif

                            @if(auth()->user()->isFreelancer())
                                <a href="{{ route('proposals.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fa-solid fa-briefcase w-5"></i>
                                    Moje oferty
                                </a>
                                <a href="{{ route('portfolio.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fa-solid fa-folder-open w-5"></i>
                                    Portfolio
                                </a>
                            @endif

                            <a href="{{ route('saved.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-bookmark w-5"></i>
                                Zapisane
                            </a>

                            <a href="{{ route('stats') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-chart-line w-5"></i>
                                Statystyki
                            </a>

                            <div class="border-t border-gray-100 my-2"></div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-gear w-5"></i>
                                Ustawienia
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                                    Wyloguj się
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    {{-- Login and Register - Hidden on mobile, shown on desktop --}}
                    <a href="{{ route('login') }}" class="hidden lg:inline-block text-gray-600 hover:text-gray-900 text-sm font-medium px-3 py-2">
                        Zaloguj
                    </a>
                    <a href="{{ route('register') }}" class="hidden lg:inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                        Rejestracja
                    </a>
                @endauth
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="lg:hidden border-t border-gray-200 py-4"
             style="display: none;">
            <nav class="flex flex-col space-y-2">
                <a href="{{ route('announcements.index') }}"
                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    Ogłoszenia
                </a>
                <a href="{{ route('leaderboard') }}"
                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    Ranking
                </a>
                @php
                    $headerPages = \App\Models\Page::inMenu('header')->with('children')->get();
                @endphp
                @foreach($headerPages as $page)
                    <x-menu-item :page="$page" />
                @endforeach

                @auth
                    @if(auth()->user()->isClient())
                        <a href="{{ route('announcements.create') }}"
                           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors mt-2">
                            <i class="fa-solid fa-plus"></i>
                            Dodaj ogłoszenie
                        </a>
                    @elseif(auth()->user()->isFreelancer())
                        <a href="{{ route('proposals.index') }}"
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fa-solid fa-briefcase mr-2"></i>
                            Moje oferty
                        </a>
                    @endif
                @else
                    {{-- Login and Register in mobile menu --}}
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <a href="{{ route('login') }}"
                           class="block text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors mb-2">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            Zaloguj
                        </a>
                        <a href="{{ route('register') }}"
                           class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Rejestracja
                        </a>
                    </div>
                @endauth
            </nav>
        </div>
    </div>
</header>
