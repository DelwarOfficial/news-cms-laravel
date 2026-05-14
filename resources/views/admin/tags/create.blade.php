@extends('admin.layouts.app')
@section('title', 'Create Tag')
@section('page-title', 'Create Tag')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        <form action="{{ route('admin.tags.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tag Name</label>
                    <input type="text" name="name" id="tag_name" value="{{ old('name') }}"
                           class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                           required placeholder="e.g. তথ্যপ্রযুক্তি">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug" id="tag_slug" value="{{ old('slug') }}"
                           class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm"
                           placeholder="Auto-generated from name">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to auto-generate. Use lowercase letters, numbers, and hyphens.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                          placeholder="Brief description of this tag (optional)">{{ old('description') }}</textarea>
            </div>

            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">SEO Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="70"
                               class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                               placeholder="e.g. {{ 'তথ্যপ্রযুক্তি - Dhaka Magazine' }}">
                        <p class="text-xs text-gray-400 mt-1">Max 70 characters. Leave empty to auto-generate from tag name.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                        <textarea name="meta_description" rows="2" maxlength="170"
                                  class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none"
                                  placeholder="Brief SEO description for this tag page">{{ old('meta_description') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Max 170 characters. Appears in search results.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.tags.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold text-sm">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm">Create Tag</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('tag_name');
    const slugInput = document.getElementById('tag_slug');
    if (!nameInput || !slugInput) return;
    let slugManuallyEdited = slugInput.value !== '';
    slugInput.addEventListener('input', function () { slugManuallyEdited = this.value !== ''; });
    nameInput.addEventListener('input', function () {
        if (slugManuallyEdited) return;
        slugInput.value = nameInput.value
            .replace(/[\u0980-\u09FF]/g, '')  // strip Bengali
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
    });
});
</script>
@endpush