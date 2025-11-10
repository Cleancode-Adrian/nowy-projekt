<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🏆 Ranking Freelancerów</h1>
            <p class="text-gray-600">Top 20 najlepszych specjalistów na platformie</p>
        </div>

        
        <div class="card mb-8">
            <div class="flex items-center gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mr-2">Sortuj według:</label>
                    <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="rating">Najwyżej oceniani</option>
                        <option value="projects">Najwięcej projektów</option>
                    </select>
                </div>
            </div>
        </div>

        
        <div class="space-y-4">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $topFreelancers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $freelancer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card hover:shadow-lg transition-shadow <?php echo e($index < 3 ? 'border-2' : ''); ?>

                            <?php echo e($index === 0 ? 'border-yellow-400 bg-gradient-to-r from-yellow-50 to-orange-50' : ''); ?>

                            <?php echo e($index === 1 ? 'border-gray-400 bg-gray-50' : ''); ?>

                            <?php echo e($index === 2 ? 'border-orange-400 bg-orange-50' : ''); ?>">

                    <div class="flex items-center gap-6">
                        
                        <div class="flex-shrink-0 text-center">
                            <!--[if BLOCK]><![endif]--><?php if($index === 0): ?>
                                <div class="text-5xl">🥇</div>
                            <?php elseif($index === 1): ?>
                                <div class="text-5xl">🥈</div>
                            <?php elseif($index === 2): ?>
                                <div class="text-5xl">🥉</div>
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-gray-600">#<?php echo e($index + 1); ?></span>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <a href="<?php echo e(route('users.profile', $freelancer)); ?>" class="flex-shrink-0">
                            <!--[if BLOCK]><![endif]--><?php if($freelancer->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $freelancer->avatar)); ?>"
                                     alt="<?php echo e($freelancer->name); ?>"
                                     class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md hover:scale-105 transition-transform">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-md hover:scale-105 transition-transform">
                                    <?php echo e(strtoupper(substr($freelancer->name, 0, 1))); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </a>

                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <a href="<?php echo e(route('users.profile', $freelancer)); ?>"
                                   class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                    <?php echo e($freelancer->name); ?>

                                </a>
                                <!--[if BLOCK]><![endif]--><?php if($freelancer->is_verified): ?>
                                    <span class="text-blue-500 text-lg" title="Zweryfikowany">✓</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <!--[if BLOCK]><![endif]--><?php if($freelancer->experience_level): ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">
                                        <?php echo e(ucfirst($freelancer->experience_level)); ?>

                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            
                            <?php if (isset($component)) { $__componentOriginald578eba83da76b39f0f2218ac6f8e26d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald578eba83da76b39f0f2218ac6f8e26d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.user-badges','data' => ['user' => $freelancer,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('user-badges'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($freelancer),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald578eba83da76b39f0f2218ac6f8e26d)): ?>
<?php $attributes = $__attributesOriginald578eba83da76b39f0f2218ac6f8e26d; ?>
<?php unset($__attributesOriginald578eba83da76b39f0f2218ac6f8e26d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald578eba83da76b39f0f2218ac6f8e26d)): ?>
<?php $component = $__componentOriginald578eba83da76b39f0f2218ac6f8e26d; ?>
<?php unset($__componentOriginald578eba83da76b39f0f2218ac6f8e26d); ?>
<?php endif; ?>

                            
                            <div class="flex items-center gap-6 mt-2 text-sm text-gray-600">
                                <!--[if BLOCK]><![endif]--><?php if($freelancer->average_rating > 0): ?>
                                    <div class="flex items-center gap-1">
                                        <?php if (isset($component)) { $__componentOriginalfa87e49ca3cdf62358bbc468aaf3394b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa87e49ca3cdf62358bbc468aaf3394b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.star-rating','data' => ['rating' => $freelancer->average_rating,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('star-rating'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rating' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($freelancer->average_rating),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa87e49ca3cdf62358bbc468aaf3394b)): ?>
<?php $attributes = $__attributesOriginalfa87e49ca3cdf62358bbc468aaf3394b; ?>
<?php unset($__attributesOriginalfa87e49ca3cdf62358bbc468aaf3394b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa87e49ca3cdf62358bbc468aaf3394b)): ?>
<?php $component = $__componentOriginalfa87e49ca3cdf62358bbc468aaf3394b; ?>
<?php unset($__componentOriginalfa87e49ca3cdf62358bbc468aaf3394b); ?>
<?php endif; ?>
                                        <span class="font-semibold text-gray-900"><?php echo e(number_format($freelancer->average_rating, 1)); ?></span>
                                        <span>(<?php echo e($freelancer->ratings_count); ?>)</span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <div>💼 <?php echo e($freelancer->completed_projects); ?> projektów</div>
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($freelancer->bio): ?>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-1"><?php echo e($freelancer->bio); ?></p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="flex-shrink-0">
                            <a href="<?php echo e(route('users.profile', $freelancer)); ?>"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                Zobacz profil
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!--[if BLOCK]><![endif]--><?php if($topFreelancers->count() === 0): ?>
            <div class="text-center py-16 bg-white rounded-xl">
                <div class="text-6xl mb-4">🏆</div>
                <p class="text-gray-500">Brak freelancerów w rankingu</p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    </div>
</div>

<?php /**PATH D:\nowy projekt\backend\resources\views/livewire/leaderboard.blade.php ENDPATH**/ ?>
