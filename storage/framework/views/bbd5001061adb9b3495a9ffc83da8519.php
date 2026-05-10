<?php $__env->startSection('title', 'Posts'); ?>
<?php $__env->startSection('page-title', 'Posts'); ?>
<?php $__env->startSection('header-actions'); ?>
    <a href="<?php echo e(route('admin.posts.create')); ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Post
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100 select-none">
                <tr>
                    <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'title', 'label' => 'Title', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Author</th>
                    <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'status', 'label' => 'Status', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Date', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900"><?php echo e(Str::limit($post->title, 60)); ?></div>
                        <div class="text-xs text-gray-400 mt-0.5"><?php echo e($post->slug); ?></div>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo e($post->author->name ?? '—'); ?></td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            <?php echo e($post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')); ?>">
                            <?php echo e(ucfirst($post->status)); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs"><?php echo e($post->created_at->format('M d, Y')); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $post)): ?>
                                <a href="<?php echo e(route('admin.posts.edit', $post)); ?>" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-pencil text-xs"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $post)): ?>
                                <form method="POST" action="<?php echo e(route('admin.posts.destroy', $post)); ?>" onsubmit="return confirm('Delete this post?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No posts yet. <a href="<?php echo e(route('admin.posts.create')); ?>" class="text-blue-600">Create one →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($posts->hasPages()): ?>
    <div class="px-6 py-4 border-t border-gray-100">
        <?php echo e($posts->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/posts/index.blade.php ENDPATH**/ ?>