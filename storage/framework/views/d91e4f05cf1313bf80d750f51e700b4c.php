

<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                📝
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Blog - Zarządzanie wpisami</h1>
                <p class="text-gray-600">Twórz i edytuj artykuły dla użytkowników</p>
            </div>
        </div>
        <a href="<?php echo e(route('admin.blog.create')); ?>" 
           class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Dodaj wpis
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-2xl"></i>
        <p class="text-green-900 font-medium"><?php echo e(session('success')); ?></p>
    </div>
<?php endif; ?>

<div class="card">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-sm font-semibold">Tytuł</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Autor</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Status</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Wyświetlenia</th>
                <th class="text-left px-4 py-3 text-sm font-semibold">Data</th>
                <th class="text-right px-4 py-3 text-sm font-semibold">Akcje</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <?php if($post->featured_image): ?>
                                <img src="<?php echo e(asset('storage/' . $post->featured_image)); ?>"
                                     class="w-16 h-16 object-cover rounded">
                            <?php endif; ?>
                            <div>
                                <div class="font-medium text-gray-900"><?php echo e($post->title); ?></div>
                                <div class="text-xs text-gray-500">/<?php echo e($post->slug); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm"><?php echo e($post->author->name); ?></td>
                    <td class="px-4 py-4">
                        <?php if($post->status === 'published'): ?>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">✅ Opublikowany</span>
                        <?php else: ?>
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">📝 Szkic</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4 text-sm"><?php echo e($post->views_count); ?></td>
                    <td class="px-4 py-4 text-sm"><?php echo e($post->created_at->format('d.m.Y')); ?></td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <?php if($post->status === 'published'): ?>
                                <a href="<?php echo e(route('blog.show', $post->slug)); ?>" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    👁️ Zobacz
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.blog.edit', $post)); ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                ✏️ Edytuj
                            </a>
                            <form method="POST" action="<?php echo e(route('admin.blog.delete', $post)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć ten wpis?')"
                                        class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    🗑️ Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="mt-6">
        <?php echo e($posts->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\nowy projekt\backend\resources\views/admin/blog/index.blade.php ENDPATH**/ ?>
