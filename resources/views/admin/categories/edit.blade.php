@extends('admin.layouts.app')
@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Parent</label>
            <select name="parent_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">None</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                <input type="text" name="meta_description" value="{{ old('meta_description', $category->meta_description) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Save Changes</button>
        </div>
    </form>
</div>
@endsection
