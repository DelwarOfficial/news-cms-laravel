@extends('admin.layouts.app')

@section('title', 'Content Placements')
@section('page-title', 'Content Placements')

@section('content')
    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            <div class="font-semibold">Please fix the highlighted fields.</div>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[360px,1fr]">
        <form method="POST" action="{{ route('admin.placements.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            @csrf
            <div>
                <h2 class="text-base font-bold text-gray-900">Assign Story</h2>
                <p class="text-sm text-gray-500 mt-1">Choose a published post and place it into a homepage slot.</p>
            </div>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Placement key</span>
                <input name="placement_key" list="placement-options" value="{{ old('placement_key', request('placement_key', 'home.featured')) }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                <datalist id="placement-options">
                    @foreach($placementOptions as $key)
                        <option value="{{ $key }}"></option>
                    @endforeach
                </datalist>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Post</span>
                <select name="post_id" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Select a published post</option>
                    @foreach($posts as $post)
                        <option value="{{ $post->id }}" @selected((int) old('post_id') === $post->id)>
                            #{{ $post->id }} {{ \Illuminate\Support\Str::limit($post->title, 80) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="grid grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Sort order</span>
                    <input type="number" min="0" max="65535" name="sort_order" value="{{ old('sort_order') }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label class="flex items-end gap-2 pb-2 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('is_active', true))>
                    Active
                </label>
            </div>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Starts at</span>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Ends at</span>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </label>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <i class="fas fa-save"></i> Save Placement
            </button>
        </form>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Current Assignments</h2>
                    <p class="text-sm text-gray-500">Active placements are read by the Dhaka Magazine homepage.</p>
                </div>
                <form method="GET" action="{{ route('admin.placements.index') }}">
                    <select name="placement_key" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All slots</option>
                        @foreach($placementOptions as $key)
                            <option value="{{ $key }}" @selected(request('placement_key') === $key)>{{ $key }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Slot</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Post</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Schedule</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($placements as $placement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $placement->placement_key }}</div>
                                <div class="text-xs text-gray-500">Order: {{ $placement->sort_order ?? 'auto' }}</div>
                                <div class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $placement->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $placement->is_active ? 'Active' : 'Inactive' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $placement->post?->title ?? 'Missing post' }}</div>
                                <div class="text-xs text-gray-500">#{{ $placement->post_id }} {{ $placement->post?->slug }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <div>Start: {{ $placement->starts_at?->format('M d, Y H:i') ?? 'Immediately' }}</div>
                                <div>End: {{ $placement->ends_at?->format('M d, Y H:i') ?? 'No expiry' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.placements.destroy', $placement) }}" class="flex justify-end" onsubmit="return confirm('Remove this placement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">No placements yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-t border-gray-100 px-6 py-4">
                {{ $placements->links() }}
            </div>
        </div>
    </div>
@endsection
