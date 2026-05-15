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
                <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4">
                    <div class="flex gap-1 bg-gray-100 rounded-lg p-0.5" id="lang-tabs">
                        <button type="button" class="lang-tab px-4 py-1.5 text-sm font-semibold rounded-md bg-white shadow-sm text-gray-900" data-lang="bn">বাংলা</button>
                        <button type="button" class="lang-tab px-4 py-1.5 text-sm font-semibold rounded-md text-gray-500 hover:text-gray-700" data-lang="en">English</button>
                    </div>
                    <button type="button" id="ai-translate-btn" class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1.5 rounded-lg font-medium transition-colors">🤖 Translate বাং → EN</button>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Shoulder</label>
                    <input type="text" name="shoulder" value="{{ old('shoulder') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="Small label above headline">
                </div>

                <div class="lang-field bn">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title (বাংলা) <span class="text-red-500">*</span></label>
                    <input type="text" name="title_bn" id="post-title-bn" value="{{ old('title_bn') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition font-bengali" required>
                </div>
                <div class="lang-field en hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title (English)</label>
                    <input type="text" name="title_en" id="post-title-en" value="{{ old('title_en') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                </div>

                <div class="lang-field bn">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug (বাংলা)</label>
                    <input type="text" name="slug_bn" id="post-slug-bn" value="{{ old('slug_bn') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="auto-generated if empty">
                </div>
                <div class="lang-field en hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug (English)</label>
                    <input type="text" name="slug_en" id="post-slug-en" value="{{ old('slug_en') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="auto-generated if empty">
                </div>

                <div class="lang-field bn">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt / Intro (বাংলা)</label>
                    <textarea name="summary_bn" rows="3" maxlength="5000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm leading-7 outline-none resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bengali" placeholder="Write a short excerpt...">{{ old('summary_bn') }}</textarea>
                    <div class="mt-1 text-xs text-gray-400 text-right"><span data-counter-for="summary_bn">0</span>/500</div>
                </div>
                <div class="lang-field en hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt / Intro (English)</label>
                    <textarea name="summary_en" rows="3" maxlength="5000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm leading-7 outline-none resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Write a short excerpt...">{{ old('summary_en') }}</textarea>
                    <div class="mt-1 text-xs text-gray-400 text-right"><span data-counter-for="summary_en">0</span>/500</div>
                </div>

                <div class="lang-field bn">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Content (বাংলা) <span class="text-red-500">*</span></label>
                    <x-forms.tinymce id="post-body-bn" name="body_bn" :value="old('body_bn')" placeholder="Write your post content here." />
                </div>
                <div class="lang-field en hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Content (English)</label>
                    <x-forms.tinymce id="post-body-en" name="body_en" :value="old('body_en')" placeholder="Write your post content here." />
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                    <h2 class="text-sm font-bold text-gray-900">Options</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
            </div>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8 space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-sm font-bold text-gray-900">SEO</h2>
                    <span class="text-xs text-gray-400">Manual values are preserved</span>
                </div>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-0.5 mb-4 w-fit" id="seo-lang-tabs">
                    <button type="button" class="seo-lang-tab px-3 py-1 text-xs font-semibold rounded-md bg-white shadow-sm text-gray-900" data-lang="bn">বাংলা</button>
                    <button type="button" class="seo-lang-tab px-3 py-1 text-xs font-semibold rounded-md text-gray-500 hover:text-gray-700" data-lang="en">English</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="seo-lang-field bn">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title (বাংলা)</label>
                        <input type="text" name="meta_title_bn" maxlength="70" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" value="{{ old('meta_title_bn') }}">
                        <div class="mt-1 text-xs text-gray-400"><span data-counter-for="meta_title_bn">0</span>/70</div>
                    </div>
                    <div class="seo-lang-field en hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">SEO Title (English)</label>
                        <input type="text" name="meta_title_en" maxlength="70" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" value="{{ old('meta_title_en') }}">
                        <div class="mt-1 text-xs text-gray-400"><span data-counter-for="meta_title_en">0</span>/70</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Canonical URL</label>
                        <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="seo-lang-field bn md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description (বাংলা)</label>
                        <textarea name="meta_description_bn" rows="3" maxlength="170" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('meta_description_bn') }}</textarea>
                        <div class="mt-1 text-xs text-gray-400"><span data-counter-for="meta_description_bn">0</span>/170</div>
                    </div>
                    <div class="seo-lang-field en hidden md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description (English)</label>
                        <textarea name="meta_description_en" rows="3" maxlength="170" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none resize-none">{{ old('meta_description_en') }}</textarea>
                        <div class="mt-1 text-xs text-gray-400"><span data-counter-for="meta_description_en">0</span>/170</div>
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
                @php $createStatus = old('status', 'draft'); @endphp
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="status" value="draft" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-3 rounded-xl font-semibold transition-colors {{ $createStatus === 'draft' ? 'ring-2 ring-black/30 ring-offset-2' : '' }}">Save Draft</button>
                    @can('posts.submit_review')
                        <button type="submit" name="status" value="pending" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-3 rounded-xl font-semibold transition-colors {{ $createStatus === 'pending' ? 'ring-2 ring-white/70 ring-offset-2' : '' }}">Review</button>
                    @endcan
                    @can('posts.publish')
                        <button type="submit" name="status" value="scheduled" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors {{ $createStatus === 'scheduled' ? 'ring-2 ring-white/70 ring-offset-2' : '' }}">Schedule</button>
                        <button type="submit" name="status" value="published" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors {{ $createStatus === 'published' ? 'ring-2 ring-white/70 ring-offset-2' : '' }}">Publish</button>
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
                <input type="text" name="featured_image_alt" id="featured-image-alt" value="{{ old('featured_image_alt') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Alt text">
                <input type="text" name="featured_image_caption" value="{{ old('featured_image_caption') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Caption">
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900">Taxonomy</h2>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            @php($categoryLabel = $cat->parent && $cat->parent->name !== $cat->name ? $cat->parent->name.' / '.$cat->name : $cat->name)
                            <option value="{{ $cat->id }}" @selected((int) old('category_id') === $cat->id)>{{ $categoryLabel }}</option>
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
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Visibility</label>
                    <select name="visibility" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="public" @selected(old('visibility', 'public') === 'public')>Public</option>
                        <option value="private" @selected(old('visibility') === 'private')>Private</option>
                    </select>
                </div>
                <div class="text-sm text-gray-500">Reading time: <span id="reading-time-estimate">1</span> min</div>
            </section>

        </aside>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('post-create-form');
    const featuredImage = document.getElementById('featured-image');
    const featuredPreview = document.getElementById('featured-image-preview');
    const readingTime = document.getElementById('reading-time-estimate');

    // ── Language Tabs ──
    function initTabs(tabContainer, fieldGroup) {
        const tabs = tabContainer?.querySelectorAll('[data-lang]');
        tabs?.forEach(tab => {
            tab.addEventListener('click', () => {
                const lang = tab.dataset.lang;
                tabs.forEach(t => t.classList.remove('bg-white', 'shadow-sm', 'text-gray-900'));
                tabs.forEach(t => t.classList.add('text-gray-500'));
                tab.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
                tab.classList.remove('text-gray-500');
                document.querySelectorAll(`.${fieldGroup}`).forEach(el => el.classList.add('hidden'));
                document.querySelectorAll(`.${fieldGroup}.${lang}`).forEach(el => el.classList.remove('hidden'));
            });
        });
    }
    initTabs(document.getElementById('lang-tabs'), 'lang-field');
    initTabs(document.getElementById('seo-lang-tabs'), 'seo-lang-field');

    // ── AI Translate ──
    document.getElementById('ai-translate-btn')?.addEventListener('click', async () => {
        const btn = document.getElementById('ai-translate-btn');
        btn.textContent = '⏳ Translating...';
        btn.disabled = true;
        try {
            const res = await fetch('{{ route("admin.posts.translate") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value },
                body: JSON.stringify({
                    title_bn: document.querySelector('input[name="title_bn"]')?.value || '',
                    summary_bn: document.querySelector('textarea[name="summary_bn"]')?.value || '',
                    body_bn: window.tinymce?.get('post-body-bn')?.getContent() || '',
                    meta_title_bn: document.querySelector('input[name="meta_title_bn"]')?.value || '',
                    meta_description_bn: document.querySelector('textarea[name="meta_description_bn"]')?.value || '',
                }),
            });
            const data = await res.json();
            if (data.title_en) document.querySelector('input[name="title_en"]').value = data.title_en;
            if (data.summary_en) document.querySelector('textarea[name="summary_en"]').value = data.summary_en;
            if (data.body_en && window.tinymce?.get('post-body-en')) window.tinymce.get('post-body-en').setContent(data.body_en);
            if (data.meta_title_en) document.querySelector('input[name="meta_title_en"]').value = data.meta_title_en;
            if (data.meta_description_en) document.querySelector('textarea[name="meta_description_en"]').value = data.meta_description_en;
        } catch (e) {
            alert('Translation failed. Check console for details.');
            console.error(e);
        }
        btn.textContent = '🤖 Translate বাং → EN';
        btn.disabled = false;
    });

    // ── Counter helpers ──
    document.querySelectorAll('[data-counter-for]').forEach(el => {
        const input = document.getElementById(el.dataset.counterFor);
        const update = () => el.textContent = input?.value.length || 0;
        input?.addEventListener('input', update);
        update();
    });

    // ── Slugify title_bn → slug_bn ──
    const titleBn = document.getElementById('post-title-bn');
    const slugBn = document.getElementById('post-slug-bn');
    const slugify = (value) => value.toLowerCase().trim().normalize('NFC').replace(/[^\p{L}\p{M}\p{N}]+/gu, '-').replace(/^-+|-+$/g, '');
    titleBn?.addEventListener('input', () => {
        if (slugBn && !slugBn.dataset.touched) slugBn.value = slugify(titleBn.value);
    });
    slugBn?.addEventListener('input', () => slugBn.dataset.touched = '1');

    // ── Submit: save TinyMCE ──
    form?.addEventListener('submit', () => window.tinymce?.triggerSave());

    // ── Featured image preview ──
    featuredImage?.addEventListener('change', () => {
        const file = featuredImage.files?.[0];
        if (!file) return;
        featuredPreview.src = URL.createObjectURL(file);
        featuredPreview.classList.remove('hidden');
    });

    // ── Location filters ──
    const divisionSelect = document.getElementById('division_id');
    const districtSelect = document.getElementById('district_id');
    const upazilaSelect = document.getElementById('upazila_id');
    const districtOptions = districtSelect ? Array.from(districtSelect.options) : [];
    const upazilaOptions = upazilaSelect ? Array.from(upazilaSelect.options) : [];
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
    divisionSelect?.addEventListener('change', filterDistricts);
    districtSelect?.addEventListener('change', filterUpazilas);
    filterDistricts();
});
</script>
@endsection
