@extends('admin.layouts.app')
@section('title', 'Create Post')
@section('page-title', 'Create Post')

@section('content')
@php
    $locale = app()->getLocale();
    $titleField = 'title_'.$locale;
    $slugField = 'slug_'.$locale;
    $summaryField = 'summary_'.$locale;
    $bodyField = 'body_'.$locale;
    $metaTitleField = 'meta_title_'.$locale;
    $metaDescriptionField = 'meta_description_'.$locale;
@endphp

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 min-w-0" id="post-create-form">
    @csrf

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-6 items-start">
        <div class="space-y-6 min-w-0">
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Shoulder</label>
                    <input type="text" name="shoulder" value="{{ old('shoulder') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="Small label above headline">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="{{ $titleField }}" id="post-title" value="{{ old($titleField) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition {{ $locale === 'bn' ? 'font-bengali' : '' }}" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug / Permalink</label>
                    <input type="text" name="{{ $slugField }}" id="post-slug" value="{{ old($slugField) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="auto-generated if empty">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt / Intro</label>
                    <textarea id="post-excerpt" name="{{ $summaryField }}" rows="3" maxlength="5000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm leading-7 outline-none resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $locale === 'bn' ? 'font-bengali' : '' }}" placeholder="Write a short excerpt...">{{ old($summaryField) }}</textarea>
                    <div class="mt-1 text-xs text-gray-400 text-right"><span data-counter-for="post-excerpt">0</span>/500</div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Content <span class="text-red-500">*</span></label>
                    <x-forms.tinymce
                        id="post-body-input"
                        name="{{ $bodyField }}"
                        :value="old($bodyField)"
                        placeholder="Write your post content here. You can use formatting, links, quotes, headings and lists."
                    />
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8 space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-sm font-bold text-gray-900">SEO</h2>
                    <span class="text-xs text-gray-400">Manual values are preserved</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title</label>
                        <input type="text" name="{{ $metaTitleField }}" id="seo-title" value="{{ old($metaTitleField) }}" maxlength="70" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        <div class="mt-1 text-xs text-gray-400"><span data-counter-for="seo-title">0</span>/70</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Canonical URL</label>
                        <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                        <textarea name="{{ $metaDescriptionField }}" id="meta-description" rows="3" maxlength="170" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old($metaDescriptionField) }}</textarea>
                        <div class="mt-1 text-xs text-gray-400"><span data-counter-for="meta-description">0</span>/170</div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Open Graph Image</label>
                        <input type="file" name="og_image" accept="image/*" class="w-full border border-gray-200 px-4 py-3 rounded-xl text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:rounded-lg">
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-6 min-w-0">
            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Publish</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Schedule Date</label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled-at" value="{{ old('scheduled_at') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="status" value="draft" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-3 rounded-xl font-semibold transition-colors">Save Draft</button>
                    @can('posts.submit_review')
                        <button type="submit" name="status" value="pending" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-3 rounded-xl font-semibold transition-colors">Review</button>
                    @endcan
                    @can('posts.publish')
                        <button type="submit" name="status" value="scheduled" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors">Schedule</button>
                        <button type="submit" name="status" value="published" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors">Publish</button>
                    @endcan
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Featured Image</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Media Library Image</label>
                    <select name="featured_media_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Select from Media Library</option>
                        @foreach(\App\Models\Media::orderByDesc('id')->limit(50)->get() as $media)
                            <option value="{{ $media->id }}" @selected((int) old('featured_media_id') === $media->id)>{{ $media->name }} ({{ $media->file_name }})</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-xs text-gray-400">Or upload a new image:</p>
                <input type="file" name="featured_image" id="featured-image" accept="image/*" class="w-full border border-gray-200 px-4 py-3 rounded-xl text-sm file:mr-4 file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:rounded-lg">
                <img id="featured-image-preview" class="hidden w-full aspect-video object-cover rounded-xl border border-gray-200" alt="">
                <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Alt text">
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Taxonomy</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((int) old('category_id') === $cat->id)>{{ $cat->parent ? $cat->parent->name.' / ' : '' }}{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tags</label>
                    <select name="tag_ids[]" multiple class="w-full min-h-36 border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tag_ids', [])))>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @can('categories.manage')
                        <a href="{{ route('admin.categories.create') }}" class="text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-xl text-sm font-semibold">Add Category</a>
                    @else
                        <button type="button" disabled class="bg-gray-100 text-gray-400 px-3 py-2 rounded-xl text-sm font-semibold cursor-not-allowed">Add Category</button>
                    @endcan
                    @can('tags.manage')
                        <a href="{{ route('admin.tags.create') }}" class="text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-xl text-sm font-semibold">Add Tag</a>
                    @else
                        <button type="button" disabled class="bg-gray-100 text-gray-400 px-3 py-2 rounded-xl text-sm font-semibold cursor-not-allowed">Add Tag</button>
                    @endcan
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Post Details</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Author</label>
                    <select name="author_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Current user</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" @selected((int) old('author_id') === $author->id)>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Post Format</label>
                    <select name="post_format" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        @foreach(['standard' => 'Standard', 'video' => 'Video', 'gallery' => 'Gallery', 'opinion' => 'Opinion', 'live' => 'Live'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('post_format', 'standard') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-sm text-gray-500">Reading time: <span id="reading-time-estimate">1</span> min</div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Options</h2>
                <div class="grid grid-cols-1 gap-3">
                    @foreach([
                        'is_featured' => 'Featured',
                        'is_breaking' => 'Breaking News',
                        'is_trending' => 'Trending',
                        'is_editors_pick' => "Editor's Pick",
                        'is_sticky' => 'Sticky',
                        'is_photocard' => 'Photocard',
                        'show_author' => 'Show Author',
                        'allow_comments' => 'Allow Comments',
                        'show_publish_date' => 'Show Publish Date',
                    ] as $field => $label)
                        <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, in_array($field, ['show_author', 'allow_comments', 'show_publish_date'], true))) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Location</h2>
                <select name="division_id" id="division_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">No Division</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" @selected((int) old('division_id') === $division->id)>{{ $division->name }}</option>
                    @endforeach
                </select>
                <select name="district_id" id="district_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">No District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" data-division="{{ $district->division_id }}" @selected((int) old('district_id') === $district->id)>{{ $district->name }}</option>
                    @endforeach
                </select>
                <select name="upazila_id" id="upazila_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">No Upazila</option>
                    @foreach($upazilas as $upazila)
                        <option value="{{ $upazila->id }}" data-district="{{ $upazila->district_id }}" @selected((int) old('upazila_id') === $upazila->id)>{{ $upazila->name }}</option>
                    @endforeach
                </select>
            </section>
        </aside>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('post-create-form');
    const title = document.getElementById('post-title');
    const slug = document.getElementById('post-slug');
    const seoTitle = document.getElementById('seo-title');
    const metaDescription = document.getElementById('meta-description');
    const excerpt = document.getElementById('post-excerpt');
    const bodyInput = document.getElementById('post-body-input');
    const bodyEditor = document.getElementById('post-body-editor');
    const featuredImage = document.getElementById('featured-image');
    const featuredPreview = document.getElementById('featured-image-preview');
    const readingTime = document.getElementById('reading-time-estimate');

    const updateCounter = (id) => {
        const input = document.getElementById(id);
        const counter = document.querySelector(`[data-counter-for="${id}"]`);
        if (input && counter) counter.textContent = input.value.length;
    };

    const slugify = (value) => value.toLowerCase().trim().replace(/[^\p{L}\p{N}]+/gu, '-').replace(/^-+|-+$/g, '');
    title?.addEventListener('input', () => {
        if (slug && !slug.dataset.touched) slug.value = slugify(title.value);
        if (seoTitle && !seoTitle.dataset.touched) seoTitle.value = title.value.slice(0, 70);
        updateCounter('seo-title');
    });
    slug?.addEventListener('input', () => slug.dataset.touched = '1');
    seoTitle?.addEventListener('input', () => { seoTitle.dataset.touched = '1'; updateCounter('seo-title'); });
    metaDescription?.addEventListener('input', () => { metaDescription.dataset.touched = '1'; updateCounter('meta-description'); });
    excerpt?.addEventListener('input', () => {
        updateCounter('post-excerpt');
        if (metaDescription && !metaDescription.dataset.touched) {
            metaDescription.value = excerpt.value.trim().replace(/\s+/g, ' ').slice(0, 170);
            updateCounter('meta-description');
        }
    });

    const updateEditorStats = () => {
        const editor = window.tinymce?.get('post-body-input');
        const bodyText = editor ? editor.getContent({ format: 'text' }) : (bodyInput?.value || '').replace(/<[^>]*>/g, ' ');
        const text = `${excerpt?.value || ''} ${bodyText}`;
        const words = text.trim().split(/\s+/).filter(Boolean).length;
        readingTime.textContent = Math.max(1, Math.ceil(words / 200));
        if (metaDescription && !metaDescription.dataset.touched) {
            metaDescription.value = text.trim().replace(/\s+/g, ' ').slice(0, 170);
            updateCounter('meta-description');
        }
    };

    form?.addEventListener('submit', () => window.tinymce?.triggerSave());

    bodyInput?.addEventListener('input', updateEditorStats);
    document.querySelectorAll('[data-command]').forEach((button) => {
        button.addEventListener('click', () => {
            bodyEditor?.focus();
            const command = button.dataset.command;
            let value = button.dataset.value || null;
            if (command === 'createLink') {
                value = prompt('Enter URL');
                if (!value) return;
            }
            document.execCommand(command, false, value);
            updateEditorStats();
        });
    });

    featuredImage?.addEventListener('change', () => {
        const file = featuredImage.files?.[0];
        if (!file) return;
        featuredPreview.src = URL.createObjectURL(file);
        featuredPreview.classList.remove('hidden');
    });

    const divisionSelect = document.getElementById('division_id');
    const districtSelect = document.getElementById('district_id');
    const upazilaSelect = document.getElementById('upazila_id');
    const districtOptions = Array.from(districtSelect.options);
    const upazilaOptions = Array.from(upazilaSelect.options);

    function filterUpazilas() {
        const districtId = districtSelect.value;
        upazilaOptions.forEach((option) => option.hidden = option.value && districtId && option.dataset.district !== districtId);
        if (upazilaSelect.selectedOptions[0]?.hidden) upazilaSelect.value = '';
    }

    function filterDistricts() {
        const divisionId = divisionSelect.value;
        districtOptions.forEach((option) => option.hidden = option.value && divisionId && option.dataset.division !== divisionId);
        if (districtSelect.selectedOptions[0]?.hidden) districtSelect.value = '';
        filterUpazilas();
    }

    divisionSelect.addEventListener('change', filterDistricts);
    districtSelect.addEventListener('change', filterUpazilas);
    filterDistricts();
    updateCounter('seo-title');
    updateCounter('meta-description');
    updateCounter('post-excerpt');
    updateEditorStats();
});
</script>
@endsection
