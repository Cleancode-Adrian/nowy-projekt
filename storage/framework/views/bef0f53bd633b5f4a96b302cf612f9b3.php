<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['announcement']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['announcement']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow border border-gray-100 overflow-hidden">
    <div class="p-6">
        
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-medium px-3 py-1 rounded-full"
                  style="background-color: <?php echo e($announcement->category->color); ?>20; color: <?php echo e($announcement->category->color); ?>">
                <?php echo e($announcement->category->name); ?>

            </span>
            <span class="text-sm text-gray-500">
                <?php echo e($announcement->created_at->diffForHumans()); ?>

            </span>
        </div>

        
        <h3 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
            <?php echo e($announcement->title); ?>

        </h3>

        
        <p class="text-gray-600 mb-4 line-clamp-3">
            <?php echo e($announcement->description); ?>

        </p>

        
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="text-green-500 mr-2">💰</span>
                    <span class="font-semibold text-gray-900">
                        <?php echo e($announcement->budget_range ?? 'Do uzgodnienia'); ?>

                    </span>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($announcement->deadline): ?>
                    <div class="flex items-center">
                        <span class="text-blue-500 mr-2">⏰</span>
                        <span class="text-sm text-gray-600"><?php echo e($announcement->deadline); ?></span>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <!--[if BLOCK]><![endif]--><?php if($announcement->is_urgent): ?>
                <span class="text-red-500 text-xl" title="Projekt pilny">🔥</span>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($announcement->tags->count() > 0): ?>
            <div class="flex flex-wrap gap-2 mb-4">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $announcement->tags->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                        <?php echo e($tag->name); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($announcement->tags->count() > 4): ?>
                    <span class="text-xs text-gray-500">+<?php echo e($announcement->tags->count() - 4); ?></span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mr-2 text-white text-xs font-bold">
                    <?php echo e(substr($announcement->user->name, 0, 1)); ?>

                </div>
                <span class="text-sm text-gray-600"><?php echo e($announcement->user->name); ?></span>
            </div>

            <div class="flex items-center gap-2">
                <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('save-button', ['announcementId' => $announcement->id]);

$__html = app('livewire')->mount($__name, $__params, 'save-'.$announcement->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <a href="<?php echo e(route('announcements.show', $announcement)); ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Zobacz szczegóły
                </a>
            </div>
        </div>
    </div>
</div>

<?php /**PATH D:\nowy projekt\backend\resources\views/components/announcement-card.blade.php ENDPATH**/ ?>