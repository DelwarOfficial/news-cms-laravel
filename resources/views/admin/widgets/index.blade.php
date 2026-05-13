@extends('admin.layouts.app')
@section('title', 'Widgets')
@section('page-title', 'Widgets')

@section('content')
<div class="grid gap-6 lg:grid-cols-[380px,1fr]">
    <form method="POST" action="{{ route('admin.widgets.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        <h2 class="text-base font-bold text-gray-900">Add Widget</h2>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Area</label>
            <select name="area" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                @foreach($areas as $area)
                    <option value="{{ $area }}" @selected(old('area') === $area)>{{ ucfirst(str_replace('_', ' ', $area)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
            <select name="type" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
            <textarea name="content" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">{{ old('content') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="enabled" value="1" class="rounded border-gray-300" @checked(old('enabled', true))>
            <span class="text-sm font-semibold text-gray-700">Enabled</span>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm">Create Widget</button>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">All Widgets</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Title</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Area</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Type</th>
                    <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($widgets as $widget)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $widget->title }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ ucfirst(str_replace('_', ' ', $widget->area)) }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ ucfirst(str_replace('_', ' ', $widget->type)) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $widget->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $widget->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.widgets.edit', $widget) }}" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.widgets.toggle', $widget) }}" class="inline">
                                    @csrf
                                    <button class="text-amber-600 hover:text-amber-800 p-1.5 rounded-lg hover:bg-amber-50">
                                        <i class="fas {{ $widget->is_active ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.widgets.destroy', $widget) }}" class="inline" onsubmit="return confirm('Delete this widget?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No widgets yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
