@extends('admin.layouts.app')
@section('title', 'Tags')
@section('page-title', 'Tags')
@section('header-actions')
    <a href="{{ route('admin.tags.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Tag
    </a>
@endsection
@section('content')
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.tags.index') }}" class="flex items-center gap-3">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search tags..." class="w-full max-w-sm border border-gray-200 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Search</button>
        </form>
    </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 select-none">
                    <tr>
                        @include('admin.partials.sortable-th', ['column' => 'name', 'label' => 'Name', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                        @include('admin.partials.sortable-th', ['column' => 'slug', 'label' => 'Slug', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                        @include('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Created', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                        <th class="px-6 py-3.5 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tags as $tag)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $tag->name }}</td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $tag->slug }}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $tag->created_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete tag?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-8 py-12 text-center text-gray-500">No tags yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($tags->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $tags->links() }}
                </div>
            @endif
        </div>
@endsection
