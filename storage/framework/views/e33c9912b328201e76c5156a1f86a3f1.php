<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('page-title', 'Site Settings'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $value = fn ($key, $default = '') => old($key, $settings[$key] ?? $default);
?>

<form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" class="max-w-5xl space-y-6">
    <?php echo csrf_field(); ?>

    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6">General</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Site Name</label>
                <input type="text" name="site_name" value="<?php echo e($value('site_name', 'NewsCore')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Email</label>
                <input type="email" name="contact_email" value="<?php echo e($value('contact_email', 'contact@newscore.com')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Site URL</label>
                <input type="url" name="site_url" value="<?php echo e($value('site_url', url('/'))); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Posts Per Page</label>
                <input type="number" name="posts_per_page" min="1" max="100" value="<?php echo e($value('posts_per_page', 12)); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Default Language</label>
                <input type="text" name="default_language" value="<?php echo e($value('default_language', 'en')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                <input type="text" name="timezone" value="<?php echo e($value('timezone', 'Asia/Dhaka')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Site Description</label>
            <textarea name="site_description" rows="3" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"><?php echo e($value('site_description', 'Professional News Content Management System')); ?></textarea>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Branding & SEO</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Logo URL</label>
                <input type="url" name="site_logo" value="<?php echo e($value('site_logo')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Favicon URL</label>
                <input type="url" name="site_favicon" value="<?php echo e($value('site_favicon')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="<?php echo e($value('meta_title')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Google Analytics ID</label>
                <input type="text" name="google_analytics_id" value="<?php echo e($value('google_analytics_id')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
            <textarea name="meta_description" rows="3" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"><?php echo e($value('meta_description')); ?></textarea>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3">
                <input type="checkbox" name="enable_comments" value="1" class="rounded border-gray-300" <?php if($value('enable_comments', '1') === '1'): echo 'checked'; endif; ?>>
                <span class="text-sm font-semibold text-gray-700">Enable Comments</span>
            </label>
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3">
                <input type="checkbox" name="require_comment_approval" value="1" class="rounded border-gray-300" <?php if($value('require_comment_approval', '1') === '1'): echo 'checked'; endif; ?>>
                <span class="text-sm font-semibold text-gray-700">Require Approval</span>
            </label>
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3">
                <input type="checkbox" name="enable_registration" value="1" class="rounded border-gray-300" <?php if($value('enable_registration', '0') === '1'): echo 'checked'; endif; ?>>
                <span class="text-sm font-semibold text-gray-700">Enable Registration</span>
            </label>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Integrations</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Host</label>
                <input type="text" name="smtp_host" value="<?php echo e($value('smtp_host')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Port</label>
                <input type="number" name="smtp_port" value="<?php echo e($value('smtp_port')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Username</label>
                <input type="text" name="smtp_username" value="<?php echo e($value('smtp_username')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Password</label>
                <input type="password" name="smtp_password" value="<?php echo e($value('smtp_password')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">reCAPTCHA Site Key</label>
                <input type="text" name="recaptcha_site_key" value="<?php echo e($value('recaptcha_site_key')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">reCAPTCHA Secret Key</label>
                <input type="password" name="recaptcha_secret_key" value="<?php echo e($value('recaptcha_secret_key')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold">Save Settings</button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>