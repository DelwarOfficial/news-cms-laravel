@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('header-actions')
    <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Post
    </a>
@endsection

@section('content')

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-newspaper text-blue-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold">{{ $stats['total_posts'] }}</div>
            <div class="text-sm text-gray-500">Total Posts</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['published_posts'] }}</div>
            <div class="text-sm text-gray-500">Published</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-amber-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-amber-600">{{ $stats['pending_posts'] }}</div>
            <div class="text-sm text-gray-500">Pending</div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-purple-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold">{{ $stats['total_users'] }}</div>
            <div class="text-sm text-gray-500">Users</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Posts --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Recent Posts</h3>
            <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentPosts as $post)
            <div class="px-6 py-3.5 flex justify-between items-center gap-4 hover:bg-gray-50 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm text-gray-900 truncate">{{ $post->title }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $post->created_at->diffForHumans() }}</div>
                </div>
                <span class="flex-shrink-0 text-xs px-2.5 py-1 rounded-full font-medium
                    {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ ucfirst($post->status) }}
                </span>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400">
                <i class="fas fa-newspaper text-3xl mb-2 block"></i>
                No posts yet.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Popular Posts --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Popular Posts</h3>
            <span class="text-sm text-gray-400">By views</span>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($popularPosts as $i => $post)
            <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                <span class="text-lg font-bold text-gray-200 w-6 text-center">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm text-gray-900 truncate">{{ Str::limit($post->title, 45) }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ number_format($post->view_count) }} views</div>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400">
                <i class="fas fa-chart-bar text-3xl mb-2 block"></i>
                No views data yet.
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection