<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">❓ Centrum Pomocy</h1>
        <p class="text-gray-600 mb-12">Najczęściej zadawane pytania</p>

        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6"><?php echo e($section['category']); ?></h2>

                <div class="space-y-4">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $section['questions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e($faq['q']); ?></h3>
                                <i class="fa-solid fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="mt-4 text-gray-700">
                                <?php echo e($faq['a']); ?>

                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

        <div class="card bg-blue-50 border-blue-200 mt-12">
            <h3 class="text-xl font-bold text-gray-900 mb-3">💬 Nie znalazłeś odpowiedzi?</h3>
            <p class="text-gray-700 mb-4">Skontaktuj się z nami:</p>
            <a href="mailto:kontakt@webfreelance.pl" class="text-blue-600 hover:text-blue-700 font-semibold">
                kontakt@webfreelance.pl
            </a>
        </div>
    </div>
</div>

<?php /**PATH D:\nowy projekt\backend\resources\views/livewire/faq.blade.php ENDPATH**/ ?>