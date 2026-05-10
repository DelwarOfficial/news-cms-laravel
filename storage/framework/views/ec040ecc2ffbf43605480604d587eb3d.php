<?php $__env->startSection('title', 'Categories'); ?>
<?php $__env->startSection('page-title', 'Categories'); ?>
<?php $__env->startSection('header-actions'); ?>
    <a href="<?php echo e(route('admin.categories.create')); ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Category
    </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'name', 'label' => 'Name', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('admin.partials.sortable-th', ['column' => 'slug', 'label' => 'Slug', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Parent</th>
                <th class="px-6 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="sortable-tbody">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50 group <?php echo e($sortBy === 'order' ? 'cursor-move' : ''); ?> bg-white" data-id="<?php echo e($category->id); ?>" draggable="<?php echo e($sortBy === 'order' ? 'true' : 'false'); ?>">
                <td class="px-6 py-4 font-medium flex items-center gap-3">
                    <i class="fas fa-grip-vertical text-gray-300 opacity-50 group-hover:opacity-100 transition"></i>
                    <?php echo e($category->name); ?>

                </td>
                <td class="px-6 py-4 text-gray-500 text-xs font-mono"><?php echo e($category->slug); ?></td>
                <td class="px-6 py-4 text-gray-500"><?php echo e($category->parent ? $category->parent->name : '—'); ?></td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50"><i class="fas fa-pencil text-xs"></i></a>
                        <form method="POST" action="<?php echo e(route('admin.categories.destroy', $category)); ?>" onsubmit="return confirm('Delete?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">No categories yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sortable-tbody');
    const manualOrderingEnabled = <?php echo json_encode($sortBy === 'order', 15, 512) ?>;
    let draggedRow = null;

    tbody.addEventListener('dragstart', function(e) {
        if (!manualOrderingEnabled) return;
        draggedRow = e.target.closest('tr');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', draggedRow.innerHTML);
        draggedRow.classList.add('opacity-50', 'bg-blue-50');
    });

    tbody.addEventListener('dragover', function(e) {
        if (!manualOrderingEnabled || !draggedRow) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const targetRow = e.target.closest('tr');
        if (targetRow && targetRow !== draggedRow && targetRow.parentNode === tbody) {
            const rect = targetRow.getBoundingClientRect();
            const next = (e.clientY - rect.top)/(rect.bottom - rect.top) > .5;
            tbody.insertBefore(draggedRow, next && targetRow.nextSibling || targetRow);
        }
    });

    tbody.addEventListener('dragend', function(e) {
        if (!manualOrderingEnabled || !draggedRow) return;
        draggedRow.classList.remove('opacity-50', 'bg-blue-50');
        draggedRow = null;
        saveNewOrder();
    });

    function saveNewOrder() {
        const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
        const orderData = rows.map((row, index) => ({
            id: row.dataset.id,
            order: index
        }));

        fetch('<?php echo e(route('admin.categories.reorder')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items: orderData })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                console.log('Order saved via Custom Vanilla JS!');
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Antigravity\news-cms-laravel\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>