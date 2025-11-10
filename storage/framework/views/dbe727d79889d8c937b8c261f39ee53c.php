

<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-12 h-12 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg">
            ✏️
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edytuj wpis</h1>
            <p class="text-gray-600"><?php echo e($post->title); ?></p>
        </div>
    </div>
</div>

<form method="POST" action="<?php echo e(route('admin.blog.update', $post)); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2 space-y-6">

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-heading text-blue-600"></i>
                    Tytuł wpisu *
                </label>
                <input type="text" name="title" value="<?php echo e(old('title', $post->title)); ?>"
                       class="w-full px-4 py-3 text-lg border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-quote-left text-purple-600"></i>
                    Zajawka (opcjonalnie)
                </label>
                <textarea name="excerpt" rows="3"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?php echo e(old('excerpt', $post->excerpt)); ?></textarea>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-green-600"></i>
                    Treść wpisu *
                </label>
                <div id="editor-container" style="height: 400px;" class="bg-white border-2 border-gray-200 rounded-lg <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></div>
                <textarea id="content" name="content" class="hidden"><?php echo e(old('content', $post->content)); ?></textarea>
                <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-xs text-gray-500 mt-2">💡 Użyj narzędzi edytora do formatowania tekstu</p>
            </div>

            
            <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-sm border-2 border-green-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-green-600"></i>
                    Optymalizacja SEO
                </h3>
                <div class="space-y-4">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-tag text-blue-600"></i>
                            Meta Tytuł (opcjonalnie)
                        </label>
                        <input type="text" name="meta_title" value="<?php echo e(old('meta_title', $post->meta_title)); ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="np. 10 Wskazówek dla Freelancerów - Zacznij Zarabiać Online"
                               maxlength="60">
                        <?php $__errorArgs = ['meta_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-2">💡 Wyświetla się w Google (maks. 60 znaków)</p>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-purple-600"></i>
                            Meta Opis (opcjonalnie)
                        </label>
                        <textarea name="meta_description" rows="3"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Opis wyświetlany w wynikach wyszukiwania Google..."
                                  maxlength="160"><?php echo e(old('meta_description', $post->meta_description)); ?></textarea>
                        <?php $__errorArgs = ['meta_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-2">💡 Wyświetla się w Google (maks. 160 znaków)</p>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-key text-orange-600"></i>
                            Słowa Kluczowe (opcjonalnie)
                        </label>
                        <input type="text" name="meta_keywords" value="<?php echo e(old('meta_keywords', $post->meta_keywords ? implode(', ', $post->meta_keywords) : '')); ?>"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="freelancer, zlecenia, praca zdalna, freelancing">
                        <?php $__errorArgs = ['meta_keywords'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-2">💡 Oddziel przecinkami, np: "freelancer, programista, webdev"</p>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-900"><strong>Wskazówka:</strong> Jeśli pozostawisz puste, system użyje tytułu i zajawki artykułu.</p>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-1 space-y-6">

            
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl shadow-sm border-2 border-blue-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-blue-600"></i>
                    Publikacja
                </h3>
                <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="draft" <?php echo e($post->status === 'draft' ? 'selected' : ''); ?>>📝 Szkic</option>
                    <option value="published" <?php echo e($post->status === 'published' ? 'selected' : ''); ?>>✅ Opublikowany</option>
                </select>
                <?php if($post->published_at): ?>
                    <p class="text-xs text-gray-600 mt-3">
                        📅 Opublikowano: <?php echo e($post->published_at->format('d.m.Y H:i')); ?>

                    </p>
                <?php endif; ?>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-pink-600"></i>
                    Zdjęcie wyróżniające
                </h3>

                <?php if($post->featured_image): ?>
                    <div class="mb-4">
                        <img src="<?php echo e(asset('storage/' . $post->featured_image)); ?>"
                             alt="Obecne zdjęcie"
                             class="w-full h-40 object-cover rounded-lg border-2 border-gray-200">
                        <p class="text-xs text-gray-500 mt-2 text-center">📸 Obecne zdjęcie wyróżniające</p>
                    </div>
                <?php else: ?>
                    <div class="mb-4 p-8 bg-gray-50 rounded-lg text-center">
                        <i class="fa-solid fa-image text-6xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Brak zdjęcia wyróżniającego</p>
                    </div>
                <?php endif; ?>

                <label for="featured_image_input"
                       class="block border-2 border-dashed border-blue-300 rounded-lg p-6 text-center hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <i class="fa-solid fa-cloud-arrow-up text-5xl text-blue-400 mb-3 block"></i>
                    <div class="text-sm font-semibold text-blue-600 mb-2">Kliknij, aby wybrać plik</div>
                    <p class="text-xs text-gray-500">
                        lub przeciągnij i upuść tutaj
                    </p>
                    <p class="text-xs text-gray-500 mt-3">
                        Maks 2MB • JPG, PNG, WebP • Zalecany: 1200x630px
                    </p>
                    <span id="selected-file-name-edit" class="mt-3 text-xs text-blue-600 font-semibold hidden"></span>
                </label>
                <input type="file"
                       name="featured_image"
                       id="featured_image_input"
                       accept="image/*"
                       class="hidden"
                       onchange="previewImageEdit(this)">
                <?php $__errorArgs = ['featured_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div id="image-preview-edit" class="mt-4 hidden">
                    <div class="bg-green-50 border-2 border-green-500 rounded-lg p-2">
                        <p class="text-xs text-green-700 mb-2 text-center font-semibold">✅ Nowy obrazek wybrany (zapisz formularz aby zastosować)</p>
                        <img id="preview-img-edit" class="w-full h-48 object-cover rounded-lg">
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-orange-600"></i>
                    Tagi/Kategorie
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                            <input type="checkbox" name="tags[]" value="<?php echo e($tag->id); ?>"
                                   <?php echo e($post->tags->contains($tag->id) ? 'checked' : ''); ?>

                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                            <span class="text-sm text-gray-700"><?php echo e($tag->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple"></i>
                    Statystyki
                </h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span>Wyświetlenia:</span>
                        <strong><?php echo e($post->views_count); ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Utworzono:</span>
                        <strong><?php echo e($post->created_at->format('d.m.Y')); ?></strong>
                    </div>
                    <?php if($post->updated_at != $post->created_at): ?>
                        <div class="flex justify-between">
                            <span>Edytowano:</span>
                            <strong><?php echo e($post->updated_at->format('d.m.Y H:i')); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mt-8 flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <a href="<?php echo e(route('admin.blog.index')); ?>" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Powrót do listy
        </a>
        <div class="flex items-center gap-4">
            <?php if($post->status === 'published'): ?>
                <a href="<?php echo e(route('blog.show', $post->slug)); ?>" target="_blank"
                   class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i>
                    Zobacz wpis
                </a>
            <?php endif; ?>
            <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                Zapisz zmiany
            </button>
        </div>
    </div>
</form>

<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
// Image preview for featured image
function previewImageEdit(input) {
    const preview = document.getElementById('image-preview-edit');
    const img = document.getElementById('preview-img-edit');
    const fileName = document.getElementById('selected-file-name-edit');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);

        if (fileName) {
            fileName.textContent = input.files[0].name;
            fileName.classList.remove('hidden');
        }
    } else if (fileName) {
        fileName.textContent = '';
        fileName.classList.add('hidden');
    }
}

// Initialize Quill Editor
var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['clean']
        ]
    },
    placeholder: 'Napisz treść artykułu...'
});

// Set initial content if exists
var contentTextarea = document.getElementById('content');
if (contentTextarea.value) {
    quill.root.innerHTML = contentTextarea.value;
}

// Update hidden textarea on form submit
document.querySelector('form').addEventListener('submit', function() {
    contentTextarea.value = quill.root.innerHTML;
});

// Also update on any change (for better safety)
quill.on('text-change', function() {
    contentTextarea.value = quill.root.innerHTML;
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\nowy projekt\backend\resources\views/admin/blog/edit.blade.php ENDPATH**/ ?>
