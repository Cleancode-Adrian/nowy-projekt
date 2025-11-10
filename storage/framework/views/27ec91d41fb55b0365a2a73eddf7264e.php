<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admina - Logowanie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    🔐
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Panel Admina</h2>
                <p class="text-gray-600">Zaloguj się aby zarządzać platformą</p>
            </div>

            <?php if($errors->any()): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('admin.login.post')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="admin@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasło</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="••••••••">
                </div>

                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Zapamiętaj mnie</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition-colors">
                    🔓 Zaloguj się
                </button>
            </form>

            <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-900 font-semibold mb-2">Dane testowe admina:</p>
                <p class="text-sm text-blue-800">Email: admin@example.com</p>
                <p class="text-sm text-blue-800">Hasło: password</p>
            </div>
        </div>
    </div>
</body>
</html>

<?php /**PATH D:\nowy projekt\backend\resources\views/admin/login.blade.php ENDPATH**/ ?>