<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <article class="card mb-8">
            <!--[if BLOCK]><![endif]--><?php if($post->featured_image): ?>
                <img src="<?php echo e(asset('storage/' . $post->featured_image)); ?>"
                     alt="<?php echo e($post->title); ?>"
                     class="w-full h-96 object-cover rounded-xl -mt-6 -mx-6 mb-8">
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="flex items-center gap-2 mb-4">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full"><?php echo e($tag->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <h1 class="text-4xl font-bold text-gray-900 mb-6"><?php echo e($post->title); ?></h1>

            <div class="flex items-center gap-6 text-sm text-gray-600 mb-8 pb-6 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <!--[if BLOCK]><![endif]--><?php if($post->author->avatar): ?>
                        <img src="<?php echo e(asset('storage/' . $post->author->avatar)); ?>"
                             class="w-10 h-10 rounded-full">
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo e($post->author->name); ?></p>
                        <p class="text-xs"><?php echo e($post->published_at->format('d.m.Y')); ?></p>
                    </div>
                </div>
                <span>📖 <?php echo e($post->reading_time); ?> min czytania</span>
                <span>👁️ <?php echo e($post->views_count); ?> wyświetleń</span>
            </div>

            <div class="prose prose-lg max-w-none">
                <?php echo $post->content; ?>

            </div>
        </article>

        <!--[if BLOCK]><![endif]--><?php if($relatedPosts->count() > 0): ?>
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">📚 Podobne artykuły</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $relatedPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('blog.show', $related->slug)); ?>" class="card hover:shadow-lg transition-shadow">
                            <!--[if BLOCK]><![endif]--><?php if($related->featured_image): ?>
                                <img src="<?php echo e(asset('storage/' . $related->featured_image)); ?>"
                                     class="w-full h-32 object-cover rounded-lg mb-3">
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <h3 class="font-bold text-gray-900 mb-2"><?php echo e($related->title); ?></h3>
                            <p class="text-xs text-gray-500"><?php echo e($related->published_at->format('d.m.Y')); ?></p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>

<?php /**PATH D:\nowy projekt\backend\resources\views/livewire/blog/blog-show.blade.php ENDPATH**/ ?>
