<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <title><?php echo $__env->yieldContent('title', 'Projekciarz.pl - Platforma ogłoszeń dla wykonawców projektów'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Znajdź idealnego wykonawcę dla swojego projektu. Tysiące zweryfikowanych specjalistów czeka na Twoje zlecenie.'); ?>">
    <meta name="keywords" content="<?php echo $__env->yieldContent('keywords', 'projekty, zlecenia, ogłoszenia, wykonawcy, specjaliści, freelancer'); ?>">
    <meta name="author" content="Projekciarz.pl">
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

    
    <meta property="og:title" content="<?php echo $__env->yieldContent('og_title', config('app.name')); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('og_description', 'Platforma łącząca klientów z profesjonalnymi wykonawcami projektów'); ?>">
    <meta property="og:image" content="<?php echo $__env->yieldContent('og_image', asset('images/og-default.jpg')); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Projekciarz.pl">
    <meta property="og:locale" content="pl_PL">

    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('twitter_title', config('app.name')); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('twitter_description', 'Platforma łącząca klientów z profesjonalnymi wykonawcami projektów'); ?>">
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('twitter_image', asset('images/og-default.jpg')); ?>">

    
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('apple-touch-icon.png')); ?>">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="antialiased">

    
    <?php echo $__env->make('components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main>
        <?php echo e($slot); ?>

    </main>

    
    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div x-data="{
             show: false,
             message: '',
             type: 'info',
             notify(msg, t = 'info') {
                 this.message = msg;
                 this.type = t;
                 this.show = true;
                 setTimeout(() => this.show = false, 3000);
             }
         }"
         x-show="show"
         x-transition
         @notify.window="notify($event.detail.message, $event.detail.type)"
         class="fixed top-4 right-4 z-50 max-w-sm">
        <div :class="{
            'bg-green-50 border-green-500 text-green-900': type === 'success',
            'bg-red-50 border-red-500 text-red-900': type === 'error',
            'bg-blue-50 border-blue-500 text-blue-900': type === 'info'
        }" class="border-l-4 p-4 rounded-lg shadow-lg">
            <p x-text="message"></p>
        </div>
    </div>

    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH D:\nowy projekt\backend\resources\views/layouts/app.blade.php ENDPATH**/ ?>