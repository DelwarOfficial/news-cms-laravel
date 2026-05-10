@extends('admin.layouts.app')
@section('title', 'Comments')
@section('page-title', 'Comments Moderation')

@section('content')
<div class="mb-6 flex flex-wrap gap-2">
    @foreach($stats as $key => $count)
        <a href="{{ request()->fullUrlWithQuery(['status' => $key, 'page' => null]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold {{ $status === $key ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:text-gray-900' }}">
            {{ ucfirst($key) }}
            <span class="{{ $status === $key ? 'bg-white/20' : 'bg-gray-100' }} px-2 py-0.5 rounded-lg text-xs">{{ $count }}</span>
        </a>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 select-none">
            <tr>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Comment</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Post</th>
                @include('admin.partials.sortable-th', ['column' => 'author_name', 'label' => 'Author', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                @include('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Date', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                <th class="px-6 py-3.5 text-right font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($comments as $comment)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">{{ Str::limit($comment->content, 80) }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $comment->post->title ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $comment->author_name ?? $comment->user?->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-gray-500 text-xs">{{ $comment->created_at?->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-right">
                    @if($comment->status !== 'approved')
                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-green-600 hover:underline">Approve</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-8 py-12 text-center text-gray-500">No comments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($comments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection
