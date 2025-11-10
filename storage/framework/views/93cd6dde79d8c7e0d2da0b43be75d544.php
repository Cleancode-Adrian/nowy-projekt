<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📝 Blog Projekciarz.pl</h1>
            <p class="text-gray-600">Porady, tutoriale i nowości ze świata freelancingu</p>
        </div>

        
        <div class="card mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" wire:model.live.debounce.300ms="search"
                       class="input" placeholder="Szukaj artykułów...">
                <select wire:model.live="tag" class="input">
                    <option value="">Wszystkie kategorie</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->slug); ?>"><?php echo e($t->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('blog.show', $post->slug)); ?>" class="block">
                    <article class="card hover:shadow-xl transition-all hover:-translate-y-1 cursor-pointer h-full">
                        <!--[if BLOCK]><![endif]--><?php if($post->featured_image): ?>
                            <img src="<?php echo e(asset('storage/' . $post->featured_image)); ?>"
                                 alt="<?php echo e($post->title); ?>"
                                 class="w-full h-48 object-cover rounded-t-xl -mt-6 -mx-6 mb-4">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 rounded-t-xl -mt-6 -mx-6 mb-4 flex items-center justify-center">
                                <i class="fa-solid fa-blog text-white text-6xl opacity-20"></i>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="flex items-center gap-2 mb-3">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded"><?php echo e($tag->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors">
                            <?php echo e($post->title); ?>

                        </h2>

                        <!--[if BLOCK]><![endif]--><?php if($post->excerpt): ?>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?php echo e($post->excerpt); ?></p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 text-sm text-gray-500 mt-auto">
                            <div class="flex items-center gap-2">
                                <!--[if BLOCK]><![endif]--><?php if($post->author->avatar): ?>
                                    <img src="<?php echo e(asset('storage/' . $post->author->avatar)); ?>"
                                         class="w-6 h-6 rounded-full">
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <span><?php echo e($post->author->name); ?></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span>📅 <?php echo e($post->published_at->format('d.m.Y')); ?></span>
                                <span>👁️ <?php echo e($post->views_count); ?></span>
                            </div>
                        </div>
                    </article>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-3 text-center py-16">
                    <p class="text-gray-500">Brak artykułów</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <?php echo e($posts->links()); ?>

    </div>
</div>

<?php /**PATH D:\nowy projekt\backend\resources\views/livewire/blog/blog-index.blade.php ENDPATH**/ ?>
