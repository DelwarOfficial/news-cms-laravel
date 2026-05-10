<?php $__env->startSection('title', 'Create Post'); ?>
<?php $__env->startSection('page-title', 'Create Post'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl">
    <form action="<?php echo e(route('admin.posts.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <?php if($errors->any()): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php $locale = app()->getLocale(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title_<?php echo e($locale); ?>" value="<?php echo e(old('title_'.$locale)); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition <?php echo e($locale === 'bn' ? 'font-bengali' : ''); ?>" placeholder="<?php echo e($locale === 'bn' ? 'বাংলা শিরোনাম...' : 'Post title...'); ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug_<?php echo e($locale); ?>" value="<?php echo e(old('slug_'.$locale)); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition <?php echo e($locale === 'bn' ? 'font-bengali' : ''); ?>" placeholder="<?php echo e($locale === 'bn' ? 'খবরের-স্লাগ' : 'auto-generated if empty'); ?>">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Summary</label>
                <?php if (isset($component)) { $__componentOriginal90dcee9dc198bc2e7c0040423950ffdf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8cb219c4cf0735bee5cac90fadf0a458::input','data' => ['id' => 'summary_'.e($locale).'','name' => 'summary_'.e($locale).'','value' => old('summary_'.$locale),'class' => 'newscore-richtext '.e($locale === 'bn' ? 'font-bengali' : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rich-text::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'summary_'.e($locale).'','name' => 'summary_'.e($locale).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('summary_'.$locale)),'class' => 'newscore-richtext '.e($locale === 'bn' ? 'font-bengali' : '').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf)): ?>
<?php $attributes = $__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf; ?>
<?php unset($__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal90dcee9dc198bc2e7c0040423950ffdf)): ?>
<?php $component = $__componentOriginal90dcee9dc198bc2e7c0040423950ffdf; ?>
<?php unset($__componentOriginal90dcee9dc198bc2e7c0040423950ffdf); ?>
<?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Content <span class="text-red-500">*</span></label>
                <?php if (isset($component)) { $__componentOriginal90dcee9dc198bc2e7c0040423950ffdf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8cb219c4cf0735bee5cac90fadf0a458::input','data' => ['id' => 'body_'.e($locale).'','name' => 'body_'.e($locale).'','value' => old('body_'.$locale),'class' => 'newscore-richtext '.e($locale === 'bn' ? 'font-bengali' : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rich-text::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'body_'.e($locale).'','name' => 'body_'.e($locale).'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('body_'.$locale)),'class' => 'newscore-richtext '.e($locale === 'bn' ? 'font-bengali' : '').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf)): ?>
<?php $attributes = $__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf; ?>
<?php unset($__attributesOriginal90dcee9dc198bc2e7c0040423950ffdf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal90dcee9dc198bc2e7c0040423950ffdf)): ?>
<?php $component = $__componentOriginal90dcee9dc198bc2e7c0040423950ffdf; ?>
<?php unset($__componentOriginal90dcee9dc198bc2e7c0040423950ffdf); ?>
<?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="draft" <?php echo e(old('status') === 'draft' ? 'selected' : ''); ?>>Draft</option>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('posts.submit_review')): ?>
                            <option value="pending" <?php echo e(old('status') === 'pending' ? 'selected' : ''); ?>>Submit for Review</option>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('posts.publish')): ?>
                            <option value="published" <?php echo e(old('status') === 'published' ? 'selected' : ''); ?>>Published</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <select name="category_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="">No Category</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title</label>
                    <input type="text" name="meta_title_<?php echo e($locale); ?>" value="<?php echo e(old('meta_title_'.$locale)); ?>" maxlength="70" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none <?php echo e($locale === 'bn' ? 'font-bengali' : ''); ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Canonical URL</label>
                    <input type="url" name="canonical_url" value="<?php echo e(old('canonical_url')); ?>" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description_<?php echo e($locale); ?>" rows="2" maxlength="170" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none <?php echo e($locale === 'bn' ? 'font-bengali' : ''); ?>"><?php echo e(old('meta_description_'.$locale)); ?></textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_breaking" value="1" <?php echo e(old('is_breaking') ? 'checked' : ''); ?> class="rounded">
                    <span class="text-sm text-gray-700">Breaking News</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured') ? 'checked' : ''); ?> class="rounded">
                    <span class="text-sm text-gray-700">Featured</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_trending" value="1" <?php echo e(old('is_trending') ? 'checked' : ''); ?> class="rounded">
                    <span class="text-sm text-gray-700">Trending</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-colors">
                <i class="fas fa-save mr-2"></i> Save Post
            </button>
            <a href="<?php echo e(route('admin.posts.index')); ?>" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-xl font-semibold transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/posts/create.blade.php ENDPATH**/ ?>