@extends('admin.layouts.app')
@section('title', 'Edit Widget')
@section('page-title', 'Edit Widget')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.widgets.update', $widget) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Area</label>
            <select name="area" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                @foreach($areas as $area)
                    <option value="{{ $area }}" @selected(old('area', $widget->area) === $area)>{{ ucfirst(str_replace('_', ' ', $area)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
            <select name="type" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(old('type', $widget->type) === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
            <input type="text" name="title" value="{{ old('title', $widget->title) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
            <textarea name="content" rows="6" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">{{ old('content', $widget->content) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="enabled" value="1" class="rounded border-gray-300" @checked(old('enabled', $widget->is_active))>
            <span class="text-sm font-semibold text-gray-700">Enabled</span>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.widgets.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Save Changes</button>
        </div>
    </form>
</div>
@endsection
