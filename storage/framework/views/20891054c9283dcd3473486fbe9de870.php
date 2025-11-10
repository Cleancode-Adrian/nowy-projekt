<div>

<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-6">
                Znajdź <span class="text-yellow-300">wykonawcę</span> strony WWW<br>
                w kilka minut
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Połączymy Cię z najlepszymi freelancerami. Publikuj zlecenia,
                porównuj oferty i wybieraj najlepszych specjalistów.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="<?php echo e(route('register')); ?>" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition-colors inline-block">
                    Zarejestruj się
                </a>
                <a href="<?php echo e(route('announcements.index')); ?>" class="bg-blue-700 hover:bg-blue-800 px-8 py-4 rounded-lg font-bold text-lg transition-colors inline-block">
                    Przeglądaj ogłoszenia
                </a>
            </div>
        </div>
    </div>
</section>


<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo e(number_format($stats['announcements'])); ?>+</div>
                <div class="text-gray-600">Aktywnych ofert</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2"><?php echo e(number_format($stats['freelancers'])); ?>+</div>
                <div class="text-gray-600">Zweryfikowanych wykonawców</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-green-600 mb-2">98%</div>
                <div class="text-gray-600">Zadowolonych klientów</div>
            </div>
        </div>
    </div>
</section>


<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Popularne kategorie</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Wybierz kategorię i znajdź specjalistę
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('announcements.index', ['category' => $category->slug])); ?>"
                   class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 p-6 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl"
                             style="background-color: <?php echo e($category->color); ?>20; color: <?php echo e($category->color); ?>;">
                            <i class="<?php echo e($category->icon); ?>"></i>
                        </div>
                        <span class="text-sm font-medium px-3 py-1 rounded-full"
                              style="background-color: <?php echo e($category->color); ?>20; color: <?php echo e($category->color); ?>">
                            <?php echo e($category->announcements_count); ?> ofert
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                        <?php echo e($category->name); ?>

                    </h3>
                    <p class="text-sm text-gray-600">
                        <?php echo e($category->description); ?>

                    </p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</section>


<!--[if BLOCK]><![endif]--><?php if($featuredAnnouncements->count() > 0): ?>
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">🔥 Pilne projekty</h2>
            <p class="text-xl text-gray-600">
                Te projekty wymagają szybkiej realizacji
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $featuredAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginal27614b8e9c0b331869e6d1efc4911ebe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27614b8e9c0b331869e6d1efc4911ebe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.announcement-card','data' => ['announcement' => $announcement]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('announcement-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['announcement' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($announcement)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27614b8e9c0b331869e6d1efc4911ebe)): ?>
<?php $attributes = $__attributesOriginal27614b8e9c0b331869e6d1efc4911ebe; ?>
<?php unset($__attributesOriginal27614b8e9c0b331869e6d1efc4911ebe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27614b8e9c0b331869e6d1efc4911ebe)): ?>
<?php $component = $__componentOriginal27614b8e9c0b331869e6d1efc4911ebe; ?>
<?php unset($__componentOriginal27614b8e9c0b331869e6d1efc4911ebe); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <div class="text-center mt-12">
            <a href="<?php echo e(route('announcements.index')); ?>" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                Zobacz wszystkie ogłoszenia
                <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->


<section id="jak-to-dziala" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Jak to działa?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Prosty proces w trzech krokach
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    1
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Dodaj ogłoszenie</h3>
                <p class="text-gray-600">
                    Opisz swój projekt, ustaw budżet i opublikuj zlecenie. To zajmie tylko 2 minuty.
                </p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    2
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Otrzymuj oferty</h3>
                <p class="text-gray-600">
                    Freelancerzy będą składać oferty. Porównaj ceny, terminy i portfolio.
                </p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-green-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">
                    3
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Wybierz najlepszego</h3>
                <p class="text-gray-600">
                    Zaakceptuj ofertę, rozpocznij współpracę i zrealizuj projekt.
                </p>
            </div>
        </div>
    </div>
</section>


<section id="dla-freelancerow" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    Jesteś freelancerem?<br>
                    <span class="text-blue-600">Znajdź zlecenia</span>
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    Dołącz do tysięcy freelancerów którzy znaleźli swoich klientów przez naszą platformę.
                </p>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fa-solid fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Setki nowych zleceń codziennie</h4>
                            <p class="text-gray-600 text-sm">Znajdź projekty dopasowane do Twoich umiejętności</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fa-solid fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Buduj swoją reputację</h4>
                            <p class="text-gray-600 text-sm">System ocen i portfolio pomogą Ci wyróżnić się</p>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fa-solid fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Bezpieczne płatności</h4>
                            <p class="text-gray-600 text-sm">Twoje wynagrodzenie jest zabezpieczone</p>
                        </div>
                    </li>
                </ul>

                <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg transition-colors">
                    Rozpocznij zarabianie
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="relative">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                    <div class="mb-6">
                        <div class="text-4xl font-bold mb-2"><?php echo e(number_format($stats['announcements'])); ?>+</div>
                        <div class="text-blue-100">Aktywnych projektów czeka na Ciebie</div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Średnia wartość zlecenia</span>
                                <span class="font-bold">3 500 PLN</span>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Średni czas realizacji</span>
                                <span class="font-bold">14 dni</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-16 bg-blue-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Gotowy na start?</h2>
        <p class="text-xl text-blue-100 mb-8">
            Dołącz do tysięcy użytkowników którzy realizują swoje projekty przez Projekciarz.pl
        </p>
        <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition-colors">
            Zarejestruj się za darmo
        </a>
    </div>
</section>


<?php $__env->startPush('head'); ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Projekciarz.pl",
    "description": "Platforma łącząca klientów z freelancerami",
    "url": "<?php echo e(url('/')); ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo e(route('announcements.index')); ?>?search={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
<?php $__env->stopPush(); ?>
</div>

<?php /**PATH D:\nowy projekt\backend\resources\views/livewire/home-page.blade.php ENDPATH**/ ?>