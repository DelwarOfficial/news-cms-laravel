<?php $__env->startSection('title', 'Tags'); ?>
<?php $__env->startSection('page-title', 'Tags'); ?>
<?php $__env->startSection('header-actions'); ?>
    <a href="<?php echo e(route('admin.tags.create')); ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Tag
    </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <form method="GET" action="<?php echo e(route('admin.tags.index')); ?>" class="flex items-center gap-3">
            <input type="search" name="search" value="<?php echo e($search); ?>" placeholder="Search tags..." class="w-full max-w-sm border border-gray-200 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <input type="hidden" name="sort_by" value="<?php echo e($sortBy); ?>">
            <input type="hidden" name="sort_direction" value="<?php echo e($sortDirection); ?>">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Search</button>
        </form>
    </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 select-none">
                    <tr>
                        <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'name', 'label' => 'Name', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'slug', 'label' => 'Slug', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Created', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <th class="px-6 py-3.5 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium"><?php echo e($tag->name); ?></td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs"><?php echo e($tag->slug); ?></td>
                        <td class="px-6 py-4 text-gray-500 text-xs"><?php echo e($tag->created_at?->format('M d, Y')); ?></td>
                        <td class="px-6 py-4 text-right">
                            <form action="<?php echo e(route('admin.tags.destroy', $tag)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete tag?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-8 py-12 text-center text-gray-500">No tags yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if($tags->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-100">
                    <?php echo e($tags->links()); ?>

                </div>
            <?php endif; ?>
        </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/tags/index.blade.php ENDPATH**/ ?>