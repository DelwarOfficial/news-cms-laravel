@extends('admin.layouts.app')
@section('title', 'Categories')
@section('page-title', 'Categories')
@section('header-actions')
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Category
    </a>
@endsection
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Name</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Slug</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Parent</th>
                <th class="px-6 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="sortable-tbody">
            @forelse($categories as $category)
            <tr class="hover:bg-gray-50 group cursor-move bg-white" data-id="{{ $category->id }}" draggable="true">
                <td class="px-6 py-4 font-medium flex items-center gap-3">
                    <i class="fas fa-grip-vertical text-gray-300 opacity-50 group-hover:opacity-100 transition"></i>
                    {{ $category->name }}
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs font-mono">{{ $category->slug }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $category->parent ? $category->parent->name : '—' }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50"><i class="fas fa-pencil text-xs"></i></a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sortable-tbody');
    let draggedRow = null;

    tbody.addEventListener('dragstart', function(e) {
        draggedRow = e.target.closest('tr');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', draggedRow.innerHTML);
        draggedRow.classList.add('opacity-50', 'bg-blue-50');
    });

    tbody.addEventListener('dragover', function(e) {
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

        fetch('{{ route('admin.categories.reorder') }}', {
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
@endsection