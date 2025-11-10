<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Przeglądaj ogłoszenia</h1>
            <p class="text-gray-600"><?php echo e($announcements->total()); ?> aktywnych projektów</p>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                
                <div class="md:col-span-2">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="🔍 Szukaj ogłoszeń..."
                        class="input">
                </div>

                
                <div>
                    <select wire:model.live="category" class="input">
                        <option value="">Wszystkie kategorie</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->slug); ?>"><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>

                
                <div>
                    <button wire:click="clearFilters" class="w-full btn btn-secondary">
                        ✖ Wyczyść filtry
                    </button>
                </div>
            </div>

            
            <div x-data="{ showAdvanced: false }" class="mt-4">
                <button @click="showAdvanced = !showAdvanced" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <span x-show="!showAdvanced">+ Filtry zaawansowane</span>
                    <span x-show="showAdvanced">- Ukryj filtry</span>
                </button>

                <div x-show="showAdvanced" x-transition class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budżet minimalny</label>
                        <input type="number" wire:model.live.debounce.500ms="minBudget" placeholder="np. 1000" class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budżet maksymalny</label>
                        <input type="number" wire:model.live.debounce.500ms="maxBudget" placeholder="np. 10000" class="input">
                    </div>
                </div>
            </div>
        </div>

        
        <div wire:loading class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="text-gray-600 mt-2">Ładowanie...</p>
        </div>

        
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Brak ogłoszeń</h3>
                    <p class="text-gray-600 mb-6">Nie znaleźliśmy ogłoszeń spełniających Twoje kryteria</p>
                    <button wire:click="clearFilters" class="btn btn-primary">
                        Wyczyść filtry
                    </button>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="mt-8">
            <?php echo e($announcements->links()); ?>

        </div>
    </div>
</div>


<!--[if BLOCK]><![endif]--><?php if($announcements->count() > 0): ?>
<?php $__env->startPush('head'); ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "itemListElement": [
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            "@type": "ListItem",
            "position": <?php echo e($index + 1); ?>,
            "item": {
                "@type": "JobPosting",
                "title": "<?php echo e($announcement->title); ?>",
                "description": "<?php echo e(Str::limit($announcement->description, 200)); ?>",
                "datePosted": "<?php echo e($announcement->created_at->toIso8601String()); ?>",
                "hiringOrganization": {
                    "@type": "Organization",
                    "name": "<?php echo e($announcement->user->company ?? $announcement->user->name); ?>"
                },
                "jobLocation": {
                    "@type": "Place",
                    "address": "<?php echo e($announcement->location ?? 'Zdalna'); ?>"
                }
            }
        }<?php echo e($loop->last ? '' : ','); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    ]
}
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

<?php /**PATH D:\nowy projekt\backend\resources\views/livewire/announcements-list.blade.php ENDPATH**/ ?>
