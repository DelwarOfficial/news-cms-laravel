<?php
    $align = $align ?? 'left';
    $class = $class ?? '';
    $nextDirection = $sortBy === $column && $sortDirection === 'asc' ? 'desc' : 'asc';
    $icon = 'fa-sort text-gray-300 opacity-50 group-hover:opacity-100';

    if ($sortBy === $column) {
        $icon = $sortDirection === 'asc' ? 'fa-sort-up text-blue-600' : 'fa-sort-down text-blue-600';
    }

    $alignClass = $align === 'right' ? 'justify-end text-right' : 'justify-start text-left';
?>

<th class="px-6 py-3.5 font-semibold text-gray-600 <?php echo e($class); ?>">
    <a href="<?php echo e(request()->fullUrlWithQuery(['sort_by' => $column, 'sort_direction' => $nextDirection, 'page' => null])); ?>"
       class="group flex items-center gap-2 hover:text-gray-900 transition-colors <?php echo e($alignClass); ?>">
        <span><?php echo e($label); ?></span>
        <i class="fas <?php echo e($icon); ?>"></i>
    </a>
</th>
<?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/partials/sortable-th.blade.php ENDPATH**/ ?>