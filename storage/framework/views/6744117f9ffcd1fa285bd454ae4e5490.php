<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admina - Projekciarz.pl</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .card { background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white flex flex-col shadow-2xl">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-code text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold">Projekciarz.pl</h1>
                </div>
                <div class="flex items-center gap-2 bg-gray-800 rounded-lg px-3 py-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-sm font-bold">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-4">Menu główne</p>

                <a href="<?php echo e(route('admin.dashboard')); ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition-all <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-500 shadow-lg' : 'hover:bg-gray-800 hover:translate-x-1'); ?>">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="<?php echo e(route('admin.users.index')); ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition-all <?php echo e(request()->routeIs('admin.users*') ? 'bg-gradient-to-r from-green-600 to-green-500 shadow-lg' : 'hover:bg-gray-800 hover:translate-x-1'); ?>">
                    <i class="fa-solid fa-users w-5"></i>
                    <span class="font-medium">Użytkownicy</span>
                </a>

                <a href="<?php echo e(route('admin.announcements')); ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition-all <?php echo e(request()->routeIs('admin.announcements*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 shadow-lg' : 'hover:bg-gray-800 hover:translate-x-1'); ?>">
                    <i class="fa-solid fa-bullhorn w-5"></i>
                    <span class="font-medium">Ogłoszenia</span>
                </a>

                <a href="<?php echo e(route('admin.blog.index')); ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition-all <?php echo e(request()->routeIs('admin.blog*') ? 'bg-gradient-to-r from-purple-600 to-pink-600 shadow-lg' : 'hover:bg-gray-800 hover:translate-x-1'); ?>">
                    <i class="fa-solid fa-newspaper w-5"></i>
                    <span class="font-medium">Blog</span>
                </a>

                <a href="<?php echo e(route('admin.ratings.index')); ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition-all <?php echo e(request()->routeIs('admin.ratings*') ? 'bg-gradient-to-r from-yellow-600 to-orange-600 shadow-lg' : 'hover:bg-gray-800 hover:translate-x-1'); ?>">
                    <i class="fa-solid fa-star w-5"></i>
                    <span class="font-medium">Opinie</span>
                    <?php
                        $pendingRatings = \App\Models\Rating::where('is_approved', false)->count();
                    ?>
                    <?php if($pendingRatings > 0): ?>
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo e($pendingRatings); ?></span>
                    <?php endif; ?>
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <a href="<?php echo e(route('home')); ?>" target="_blank"
                   class="flex items-center gap-3 px-4 py-3 mb-2 rounded-lg hover:bg-gray-800 transition-all">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5"></i>
                    <span class="font-medium">Zobacz stronę</span>
                </a>
                <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-900 hover:text-white transition-all text-red-400">
                        <i class="fa-solid fa-right-from-bracket w-5"></i>
                        <span class="font-medium">Wyloguj się</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <header class="bg-white border-b border-gray-200 px-8 py-6">
                <h2 class="text-2xl font-bold text-gray-900"><?php echo $__env->yieldContent('title'); ?></h2>
            </header>

            <div class="p-8">
                <?php if(session('success')): ?>
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        ✅ <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        ❌ <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>
</body>
</html>

<?php /**PATH D:\nowy projekt\backend\resources\views/admin/layout.blade.php ENDPATH**/ ?>
