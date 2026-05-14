@extends('admin.layouts.app')
@section('title', 'New Category')
@section('page-title', 'New Category')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="cat_name" value="{{ old('name') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="cat_slug" value="{{ old('slug') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="Auto-generated from name">
                <p class="text-xs text-gray-400 mt-1">Leave empty to auto-generate.</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Parent</label>
            <select name="parent_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">None</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Color</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" value="{{ old('color', '#3b82f6') }}" class="h-10 w-16 rounded-lg border border-gray-200 cursor-pointer">
                <input type="text" name="color_preview" value="{{ old('color', '#3b82f6') }}" class="flex-1 border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="#3b82f6" readonly>
            </div>
        </div>

        <hr class="border-gray-100">

        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">SEO Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="70"
                           class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                           placeholder="e.g. {{ 'খেলাধুলা - Dhaka Magazine' }}">
                    <p class="text-xs text-gray-400 mt-1">Max 70 characters.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" rows="2" maxlength="170"
                              class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none"
                              placeholder="Brief SEO description for this category page">{{ old('meta_description') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Max 170 characters.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Create Category</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('cat_name');
    const slugInput = document.getElementById('cat_slug');
    if (!nameInput || !slugInput) return;
    let slugManuallyEdited = slugInput.value !== '';
    slugInput.addEventListener('input', function () { slugManuallyEdited = this.value !== ''; });
    nameInput.addEventListener('input', function () {
        if (slugManuallyEdited) return;
        slugInput.value = nameInput.value
            .replace(/[\u0980-\u09FF]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
    });
});
</script>
@endpush
