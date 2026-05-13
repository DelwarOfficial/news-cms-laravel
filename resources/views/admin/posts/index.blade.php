@extends('admin.layouts.app')
@section('title', 'Posts')
@section('page-title', 'Posts')
@section('header-actions')
    <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Post
    </a>
@endsection

@section('content')
@php
    $statuses = [
        'all' => ['label' => 'All', 'count' => $statusCounts->sum()],
        'published' => ['label' => 'Published', 'count' => $statusCounts['published'] ?? 0],
        'draft' => ['label' => 'Drafts', 'count' => $statusCounts['draft'] ?? 0],
        'pending' => ['label' => 'Review', 'count' => $statusCounts['pending'] ?? 0],
        'scheduled' => ['label' => 'Scheduled', 'count' => $statusCounts['scheduled'] ?? 0],
    ];

    $statusClass = fn ($value) => match ($value) {
        'published' => 'bg-green-100 text-green-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'scheduled' => 'bg-indigo-100 text-indigo-700',
        'archived' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="space-y-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                @foreach($statuses as $value => $item)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => null]) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition-colors {{ $status === $value ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        <span>{{ $item['label'] }}</span>
                        <span class="rounded-lg px-1.5 py-0.5 text-xs {{ $status === $value ? 'bg-white/20 text-white' : 'bg-white text-gray-500' }}">{{ $item['count'] }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-col sm:flex-row gap-3 lg:min-w-[520px]">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search title or slug..." class="w-full rounded-xl border border-gray-200 pl-9 pr-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <select name="per_page" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    @foreach([10, 15, 25, 50] as $size)
                        <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}/page</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Filter</button>
                @if($search !== '' || $status !== 'all')
                    <a href="{{ route('admin.posts.index') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 text-center">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 select-none">
                    <tr>
                        @include('admin.partials.sortable-th', ['column' => 'title', 'label' => 'Post', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Category</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Author</th>
                        @include('admin.partials.sortable-th', ['column' => 'status', 'label' => 'Status', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                        @include('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Date', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                        <th class="px-6 py-3.5 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 min-w-[320px]">
                                <div class="font-semibold text-gray-900">{{ Str::limit($post->title, 72) }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                                    <span class="font-mono">{{ $post->slug }}</span>
                                    @if($post->is_featured)<span class="text-blue-600">Featured</span>@endif
                                    @if($post->is_breaking)<span class="text-red-600">Breaking</span>@endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $post->primaryCategory?->name ?? $post->categories->first()?->name ?? 'Uncategorized' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $post->bylineAuthor?->name ?? $post->author?->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $statusClass($post->status) }}">{{ ucfirst($post->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                <div>{{ $post->created_at->format('M d, Y') }}</div>
                                <div class="text-gray-400">{{ $post->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 justify-end">
                                    @if($post->status === 'published')
                                        <a href="{{ route('article.id_slug', ['postId' => $post->id, 'slug' => $post->slug]) }}" target="_blank" class="text-gray-500 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-100 transition-colors" title="View">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    @endif
                                    @can('update', $post)
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="Edit">
                                            <i class="fas fa-pencil text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $post)
                                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <div class="mx-auto w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 mb-3"><i class="fas fa-newspaper"></i></div>
                                <div class="font-semibold text-gray-700">No posts found</div>
                                <div class="text-sm text-gray-400 mt-1">Try changing your search or create a new post.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden divide-y divide-gray-100">
            @forelse($posts as $post)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 leading-snug">{{ $post->title }}</div>
                            <div class="text-xs text-gray-400 mt-1 font-mono break-all">{{ $post->slug }}</div>
                        </div>
                        <span class="shrink-0 text-xs px-2.5 py-1 rounded-full font-semibold {{ $statusClass($post->status) }}">{{ ucfirst($post->status) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs text-gray-500">
                        <div><span class="text-gray-400">Category</span><br>{{ $post->primaryCategory?->name ?? $post->categories->first()?->name ?? 'Uncategorized' }}</div>
                        <div><span class="text-gray-400">Author</span><br>{{ $post->bylineAuthor?->name ?? $post->author?->name ?? '—' }}</div>
                        <div><span class="text-gray-400">Created</span><br>{{ $post->created_at->format('M d, Y') }}</div>
                        <div><span class="text-gray-400">Flags</span><br>{{ collect([$post->is_featured ? 'Featured' : null, $post->is_breaking ? 'Breaking' : null])->filter()->join(', ') ?: 'None' }}</div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-1">
                        @if($post->status === 'published')
                            <a href="{{ route('article.id_slug', ['postId' => $post->id, 'slug' => $post->slug]) }}" target="_blank" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600">View</a>
                        @endif
                        @can('update', $post)
                            <a href="{{ route('admin.posts.edit', $post) }}" class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Edit</a>
                        @endcan
                        @can('delete', $post)
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-semibold text-red-600">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-gray-400">No posts found.</div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-semibold text-gray-700">{{ $posts->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold text-gray-700">{{ $posts->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-gray-700">{{ $posts->total() }}</span> posts
                </div>
                @if($posts->hasPages())
                    <div class="flex flex-wrap items-center gap-2">
                        @if($posts->onFirstPage())
                            <span class="rounded-xl border border-gray-100 px-3 py-2 text-sm text-gray-300">Previous</span>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">Previous</a>
                        @endif

                        @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="rounded-xl px-3 py-2 text-sm font-semibold {{ $posts->currentPage() === $page ? 'bg-blue-600 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' }}">{{ $page }}</a>
                        @endforeach

                        @if($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">Next</a>
                        @else
                            <span class="rounded-xl border border-gray-100 px-3 py-2 text-sm text-gray-300">Next</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
