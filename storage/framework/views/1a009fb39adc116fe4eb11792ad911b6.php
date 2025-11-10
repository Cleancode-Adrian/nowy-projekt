<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50" x-data="{ userMenuOpen: false, mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-code text-white text-sm"></i>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gray-900">WebFreelance</span>
                    </a>
                </div>

                
                <nav class="hidden md:ml-10 lg:flex md:space-x-6">
                    <a href="<?php echo e(route('announcements.index')); ?>" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Ogłoszenia
                    </a>
                    <a href="<?php echo e(route('leaderboard')); ?>" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Ranking
                    </a>
                    <a href="<?php echo e(route('blog.index')); ?>" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Blog
                    </a>
                    <a href="<?php echo e(route('faq')); ?>" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        FAQ
                    </a>
                </nav>
            </div>

            
            <div class="flex items-center space-x-2 sm:space-x-4">
                <?php if(auth()->guard()->check()): ?>
                    
                    <?php if(auth()->user()->isClient()): ?>
                        <a href="<?php echo e(route('announcements.create')); ?>"
                           class="hidden lg:flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            Dodaj ogłoszenie
                        </a>
                    <?php elseif(auth()->user()->isFreelancer()): ?>
                        <a href="<?php echo e(route('proposals.index')); ?>"
                           class="hidden lg:flex items-center gap-2 text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fa-solid fa-briefcase"></i>
                            Moje oferty
                        </a>
                    <?php endif; ?>

                    
                    <a href="<?php echo e(route('messages.index')); ?>" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fa-solid fa-envelope text-xl"></i>
                        <?php
                            $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
                        ?>
                        <?php if($unreadMessages > 0): ?>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                <?php echo e($unreadMessages > 9 ? '9+' : $unreadMessages); ?>

                            </span>
                        <?php endif; ?>
                    </a>

                    
                    <a href="<?php echo e(route('notifications')); ?>" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fa-solid fa-bell text-xl"></i>
                        <?php
                            $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                        ?>
                        <?php if($unreadNotifications > 0): ?>
                            <span class="absolute top-0 right-0 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                <?php echo e($unreadNotifications > 9 ? '9+' : $unreadNotifications); ?>

                            </span>
                        <?php endif; ?>
                    </a>

                    
                    <div class="relative" @click.away="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <?php if(auth()->user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>"
                                     alt="<?php echo e(auth()->user()->name); ?>"
                                     class="w-9 h-9 rounded-full object-cover border-2 border-gray-200">
                            <?php else: ?>
                                <div class="w-9 h-9 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold border-2 border-gray-200">
                                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                                </div>
                            <?php endif; ?>
                            <i class="fa-solid fa-chevron-down text-xs hidden sm:block"></i>
                        </button>

                        
                        <div x-show="userMenuOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2"
                             style="display: none;">

                            
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900"><?php echo e(auth()->user()->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(auth()->user()->email); ?></p>
                                <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                    <?php echo e(auth()->user()->isClient() ? '👤 Klient' : '💼 Freelancer'); ?>

                                </span>
                            </div>

                            
                            <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-gauge w-5"></i>
                                Dashboard
                            </a>

                            <?php if(auth()->user()->isClient()): ?>
                                <a href="<?php echo e(route('announcements.create')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 lg:hidden">
                                    <i class="fa-solid fa-plus w-5"></i>
                                    Dodaj ogłoszenie
                                </a>
                            <?php endif; ?>

                            <?php if(auth()->user()->isFreelancer()): ?>
                                <a href="<?php echo e(route('proposals.index')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fa-solid fa-briefcase w-5"></i>
                                    Moje oferty
                                </a>
                                <a href="<?php echo e(route('portfolio.index')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fa-solid fa-folder-open w-5"></i>
                                    Portfolio
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo e(route('saved.index')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-bookmark w-5"></i>
                                Zapisane
                            </a>

                            <a href="<?php echo e(route('stats')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-chart-line w-5"></i>
                                Statystyki
                            </a>

                            <div class="border-t border-gray-100 my-2"></div>

                            <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fa-solid fa-gear w-5"></i>
                                Ustawienia
                            </a>

                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                                    Wyloguj się
                                </button>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        Zaloguj się
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                        Zarejestruj się
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<?php /**PATH D:\nowy projekt\backend\resources\views/components/header.blade.php ENDPATH**/ ?>