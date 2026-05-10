@extends('admin.layouts.app')
@section('title', 'Posts')
@section('page-title', 'Posts')
@section('header-actions')
    <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Post
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100 select-none">
                <tr>
                    @include('admin.partials.sortable-th', ['column' => 'title', 'label' => 'Title', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Author</th>
                    @include('admin.partials.sortable-th', ['column' => 'status', 'label' => 'Status', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                    @include('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Date', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ Str::limit($post->title, 60) }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $post->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $post->author->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $post->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            @can('update', $post)
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-pencil text-xs"></i>
                                </a>
                            @endcan
                            @can('delete', $post)
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No posts yet. <a href="{{ route('admin.posts.create') }}" class="text-blue-600">Create one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
