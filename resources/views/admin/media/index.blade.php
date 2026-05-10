@extends('admin.layouts.app')
@section('title', 'Media Library')
@section('page-title', 'Media Library')

@section('header-actions')
    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
        @csrf
        <input type="file" name="file" required class="text-sm">
        <select name="folder_id" class="border border-gray-200 px-3 py-2 rounded-xl text-sm">
            <option value="">Root Folder</option>
            @foreach($folders as $folder)
                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Upload</button>
    </form>
@endsection

@section('content')
@php
    $sortOptions = [
        'created_at' => 'Date',
        'name' => 'Name',
        'file_type' => 'Type',
        'file_size' => 'Size',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-center gap-2">
    @foreach($sortOptions as $column => $label)
        @php
            $nextDirection = $sortBy === $column && $sortDirection === 'asc' ? 'desc' : 'asc';
            $active = $sortBy === $column;
        @endphp
        <a href="{{ request()->fullUrlWithQuery(['sort_by' => $column, 'sort_direction' => $nextDirection, 'page' => null]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold {{ $active ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:text-gray-900' }}">
            {{ $label }}
            <i class="fas {{ $active ? ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-gray-300' }}"></i>
        </a>
    @endforeach
</div>

<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-6">
    @forelse($media as $item)
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden group shadow-sm">
            <div class="h-40 bg-gray-100 flex items-center justify-center relative">
                @if(str_starts_with($item->file_type, 'image'))
                    <img src="{{ $item->file_url }}" class="max-h-full max-w-full object-contain" alt="">
                @else
                    <div class="text-center">
                        <i class="fas fa-file text-4xl text-gray-400"></i>
                    </div>
                @endif

                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs" onclick="return confirm('Delete?')">&times;</button>
                </form>
            </div>

            <div class="p-4 text-sm">
                <div class="font-medium truncate">{{ $item->name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ number_format($item->file_size / 1024, 1) }} KB</div>
                <div class="text-xs text-gray-400 mt-1">{{ $item->created_at?->format('M d, Y') }}</div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-20 text-gray-500">No media files yet. Upload your first file.</div>
    @endforelse
</div>

@if($media->hasPages())
    <div class="mt-6">
        {{ $media->links() }}
    </div>
@endif
@endsection
