@extends('admin.layouts.app')
@section('title', 'Content Placements')
@section('page-title', 'Content Placements')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-layer-group text-blue-600 text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500">Total Placements</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                <div class="text-sm text-gray-500">Active</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-amber-600 text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['slots_used'] }}</div>
                <div class="text-sm text-gray-500">Slots Used</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-th-large text-purple-600 text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['slots_total'] }}</div>
                <div class="text-sm text-gray-500">Total Slots</div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[380px,1fr] items-start">

        {{-- Assign Form --}}
        <form method="POST" action="{{ route('admin.placements.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
            @csrf
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-600"></i> Assign Story to Slot
                </h2>
                <p class="text-xs text-gray-500 mt-1">Choose a slot and pick a published post.</p>
            </div>

            @if($errors->any())
                <div class="mx-6 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slot</label>
                    <select name="placement_key" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 bg-white" required>
                        <option value="">Select a slot...</option>
                        @foreach($slots as $key => $label)
                            <option value="{{ $key }}" @selected(old('placement_key', request('slot')) === $key)>
                                {{ $label }} ({{ $key }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Post</label>
                    <select name="post_id" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 bg-white" required>
                        <option value="">Choose a published post...</option>
                        @foreach($posts as $post)
                            <option value="{{ $post->id }}" @selected((int) old('post_id') === $post->id)>
                                #{{ $post->id }} — {{ \Illuminate\Support\Str::limit($post->title, 70) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sort Order</label>
                    <input type="number" min="0" max="65535" name="sort_order" value="{{ old('sort_order') }}" placeholder="Auto" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Starts At</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ends At</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('is_active', true))>
                    <span class="text-sm font-semibold text-gray-700">Active immediately</span>
                </label>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-save"></i> Save Placement
                </button>
            </div>
        </form>

        {{-- Assignments Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between flex-wrap gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Current Assignments</h2>
                    <p class="text-xs text-gray-500">Active placements are consumed by the homepage.</p>
                </div>
                <form method="GET" action="{{ route('admin.placements.index') }}">
                    <select name="slot" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                        <option value="">All slots</option>
                        @foreach($slots as $key => $label)
                            <option value="{{ $key }}" @selected(request('slot') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Slot</th>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Post</th>
                            <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Schedule / Status</th>
                            <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($placements as $placement)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $slots->get($placement->placement_key, $placement->placement_key) }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono">{{ $placement->placement_key }}</div>
                                    <div class="mt-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold
                                        {{ $placement->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        <i class="fas {{ $placement->is_active ? 'fa-circle text-green-500' : 'fa-circle text-gray-400' }}" style="font-size:6px"></i>
                                        {{ $placement->is_active ? 'Active' : 'Inactive' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($placement->post)
                                        <div class="flex items-center gap-3">
                                            @if($placement->post->featured_image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($placement->post->featured_image) }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-file-alt text-gray-300 text-sm"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-medium text-gray-900 truncate max-w-[280px]">{{ $placement->post->title }}</div>
                                                <div class="text-xs text-gray-400">
                                                    #{{ $placement->post_id }}
                                                    @if($placement->post->published_at)
                                                        · {{ $placement->post->published_at->format('M d, Y') }}
                                                    @endif
                                                    @if($placement->sort_order !== null)
                                                        · Order {{ $placement->sort_order }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-red-500 text-sm">Missing post (ID: {{ $placement->post_id }})</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 {{ $placement->starts_at && $placement->starts_at->isFuture() ? 'text-amber-600' : 'text-gray-500' }}">
                                            <i class="fas fa-play" style="font-size:8px"></i>
                                            {{ $placement->starts_at?->format('M d, Y H:i') ?? 'Immediate' }}
                                        </div>
                                        <div class="flex items-center gap-1.5 {{ $placement->ends_at && $placement->ends_at->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                            <i class="fas fa-stop" style="font-size:8px"></i>
                                            {{ $placement->ends_at?->format('M d, Y H:i') ?? 'No expiry' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.placements.edit', $placement) }}"
                                           class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="Edit">
                                            <i class="fas fa-pencil text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.placements.destroy', $placement) }}"
                                              onsubmit="return confirm('Remove this placement?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Remove">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <div class="mx-auto w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 mb-3">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="font-semibold text-gray-700">No placements yet</div>
                                    <div class="text-sm text-gray-400 mt-1">Assign a post to a homepage slot using the form.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($placements->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $placements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
