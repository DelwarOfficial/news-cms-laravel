<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('header-actions'); ?>
    <a href="<?php echo e(route('admin.posts.create')); ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Post
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-newspaper text-blue-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold"><?php echo e($stats['total_posts']); ?></div>
            <div class="text-sm text-gray-500">Total Posts</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-green-600"><?php echo e($stats['published_posts']); ?></div>
            <div class="text-sm text-gray-500">Published</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-amber-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-amber-600"><?php echo e($stats['pending_posts']); ?></div>
            <div class="text-sm text-gray-500">Pending</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-purple-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold"><?php echo e($stats['total_users']); ?></div>
            <div class="text-sm text-gray-500">Users</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Recent Posts</h3>
            <a href="<?php echo e(route('admin.posts.index')); ?>" class="text-sm text-blue-600 hover:underline">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $recentPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-6 py-3.5 flex justify-between items-center gap-4 hover:bg-gray-50 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm text-gray-900 truncate"><?php echo e($post->title); ?></div>
                    <div class="text-xs text-gray-400 mt-0.5"><?php echo e($post->created_at->diffForHumans()); ?></div>
                </div>
                <span class="flex-shrink-0 text-xs px-2.5 py-1 rounded-full font-medium
                    <?php echo e($post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')); ?>">
                    <?php echo e(ucfirst($post->status)); ?>

                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-10 text-center text-gray-400">
                <i class="fas fa-newspaper text-3xl mb-2 block"></i>
                No posts yet.
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Popular Posts</h3>
            <span class="text-sm text-gray-400">By views</span>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $popularPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                <span class="text-lg font-bold text-gray-200 w-6 text-center"><?php echo e($i + 1); ?></span>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm text-gray-900 truncate"><?php echo e(Str::limit($post->title, 45)); ?></div>
                    <div class="text-xs text-gray-400 mt-0.5"><?php echo e(number_format($post->view_count)); ?> views</div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-10 text-center text-gray-400">
                <i class="fas fa-chart-bar text-3xl mb-2 block"></i>
                No views data yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>