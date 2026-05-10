@extends('admin.layouts.app')
@section('title', 'Edit Post')
@section('page-title', 'Edit Post')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            @php $locale = app()->getLocale(); @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title_{{ $locale }}" value="{{ old('title_'.$locale, $post->{'title_'.$locale} ?: ($locale === 'en' ? $post->title : '')) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition {{ $locale === 'bn' ? 'font-bengali' : '' }}" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                    <input type="text" name="slug_{{ $locale }}" value="{{ old('slug_'.$locale, $post->{'slug_'.$locale} ?: ($locale === 'en' ? $post->slug : '')) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition {{ $locale === 'bn' ? 'font-bengali' : '' }}">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Summary</label>
                <x-rich-text::input id="summary_{{ $locale }}" name="summary_{{ $locale }}" :value="old('summary_'.$locale, $post->editorHtml('summary_'.$locale))" class="newscore-richtext {{ $locale === 'bn' ? 'font-bengali' : '' }}" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Content <span class="text-red-500">*</span></label>
                <x-rich-text::input id="body_{{ $locale }}" name="body_{{ $locale }}" :value="old('body_'.$locale, $post->editorHtml('body_'.$locale))" class="newscore-richtext {{ $locale === 'bn' ? 'font-bengali' : '' }}" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        @can('posts.submit_review')
                            <option value="pending" {{ old('status', $post->status) === 'pending' ? 'selected' : '' }}>Submit for Review</option>
                        @endcan
                        @can('posts.publish')
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                        @endcan
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <select name="category_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-white">
                        <option value="">No Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $post->categories->first()?->id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title</label>
                    <input type="text" name="meta_title_{{ $locale }}" value="{{ old('meta_title_'.$locale, $post->{'meta_title_'.$locale} ?: ($locale === 'en' ? $post->meta_title : '')) }}" maxlength="70" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none {{ $locale === 'bn' ? 'font-bengali' : '' }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Canonical URL</label>
                    <input type="url" name="canonical_url" value="{{ old('canonical_url', $post->canonical_url) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description_{{ $locale }}" rows="2" maxlength="170" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none {{ $locale === 'bn' ? 'font-bengali' : '' }}">{{ old('meta_description_'.$locale, $post->{'meta_description_'.$locale} ?: ($locale === 'en' ? $post->meta_description : '')) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-colors">
                <i class="fas fa-save mr-2"></i> Update Post
            </button>
            <a href="{{ route('admin.posts.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-xl font-semibold transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
