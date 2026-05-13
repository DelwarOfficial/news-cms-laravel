@extends('admin.layouts.app')
@section('title', 'New Advertisement')
@section('page-title', 'New Advertisement')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
            <select name="position" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                <option value="">Select slot</option>
                @foreach($positions as $position)
                    <option value="{{ $position }}" @selected(old('position') === $position)>{{ ucfirst(str_replace(['-', '_'], ' ', $position)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
            <select name="type" id="ad-type" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>

        <div id="image-field">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Image (max 5MB)</label>
            <input type="file" name="image" accept="image/*" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div id="code-field" style="display:none">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Ad Code</label>
            <textarea name="code" rows="5" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm">{{ old('code') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">URL</label>
            <input type="url" name="url" value="{{ old('url') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="https://example.com">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', true))>
            <span class="text-sm font-semibold text-gray-700">Active</span>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.advertisements.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Create Advertisement</button>
        </div>
    </form>
</div>

<script>
document.getElementById('ad-type')?.addEventListener('change', function() {
    document.getElementById('image-field').style.display = this.value === 'image' ? '' : 'none';
    document.getElementById('code-field').style.display = this.value === 'code' ? '' : 'none';
});
</script>
@endsection
