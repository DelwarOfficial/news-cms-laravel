@extends('admin.layouts.app')
@section('title', 'Edit Placement')
@section('page-title', 'Edit Placement')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.placements.update', $placement) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @csrf @method('PUT')

        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="fas fa-pencil text-blue-600"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900">Edit Placement</h2>
                <p class="text-xs text-gray-500">{{ $placement->placement_key }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mx-6 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="p-6 space-y-5">
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200">
                @if($placement->post?->featured_image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($placement->post->featured_image) }}" alt="" class="w-14 h-14 rounded-xl object-cover border border-gray-100">
                @else
                    <div class="w-14 h-14 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                        <i class="fas fa-file-alt text-gray-300 text-lg"></i>
                    </div>
                @endif
                <div>
                    <div class="font-semibold text-gray-900">{{ $placement->post?->title ?? 'Missing post' }}</div>
                    <div class="text-xs text-gray-500">#{{ $placement->post_id }} · {{ $placement->placement_key }}</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Post</label>
                <select name="post_id" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                    <option value="">Keep current</option>
                    @foreach($posts as $post)
                        <option value="{{ $post->id }}" @selected((int) old('post_id', $placement->post_id) === $post->id)>
                            #{{ $post->id }} — {{ \Illuminate\Support\Str::limit($post->title, 70) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sort Order</label>
                <input type="number" min="0" max="65535" name="sort_order" value="{{ old('sort_order', $placement->sort_order) }}" placeholder="Auto" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Starts At</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $placement->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ends At</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $placement->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('is_active', $placement->is_active))>
                <span class="text-sm font-semibold text-gray-700">Active</span>
            </label>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('admin.placements.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Update Placement
            </button>
        </div>
    </form>
</div>
@endsection
