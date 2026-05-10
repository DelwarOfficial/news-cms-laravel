<?php $__env->startSection('title', 'Media Library'); ?>
<?php $__env->startSection('page-title', 'Media Library'); ?>

<?php $__env->startSection('header-actions'); ?>
    <form action="<?php echo e(route('admin.media.store')); ?>" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
        <?php echo csrf_field(); ?>
        <input type="file" name="file" required class="text-sm">
        <select name="folder_id" class="border border-gray-200 px-3 py-2 rounded-xl text-sm">
            <option value="">Root Folder</option>
            <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($folder->id); ?>"><?php echo e($folder->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Upload</button>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $sortOptions = [
        'created_at' => 'Date',
        'name' => 'Name',
        'file_type' => 'Type',
        'file_size' => 'Size',
    ];
?>

<div class="mb-6 flex flex-wrap items-center gap-2">
    <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $nextDirection = $sortBy === $column && $sortDirection === 'asc' ? 'desc' : 'asc';
            $active = $sortBy === $column;
        ?>
        <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => $column, 'sort_direction' => $nextDirection, 'page' => null])); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold <?php echo e($active ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:text-gray-900'); ?>">
            <?php echo e($label); ?>

            <i class="fas <?php echo e($active ? ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-gray-300'); ?>"></i>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-6">
    <?php $__empty_1 = true; $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden group shadow-sm">
            <div class="h-40 bg-gray-100 flex items-center justify-center relative">
                <?php if(str_starts_with($item->file_type, 'image')): ?>
                    <img src="<?php echo e($item->file_url); ?>" class="max-h-full max-w-full object-contain" alt="">
                <?php else: ?>
                    <div class="text-center">
                        <i class="fas fa-file text-4xl text-gray-400"></i>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('admin.media.destroy', $item)); ?>" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs" onclick="return confirm('Delete?')">&times;</button>
                </form>
            </div>

            <div class="p-4 text-sm">
                <div class="font-medium truncate"><?php echo e($item->name); ?></div>
                <div class="text-xs text-gray-500 mt-1"><?php echo e(number_format($item->file_size / 1024, 1)); ?> KB</div>
                <div class="text-xs text-gray-400 mt-1"><?php echo e($item->created_at?->format('M d, Y')); ?></div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-20 text-gray-500">No media files yet. Upload your first file.</div>
    <?php endif; ?>
</div>

<?php if($media->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($media->links()); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/media/index.blade.php ENDPATH**/ ?>