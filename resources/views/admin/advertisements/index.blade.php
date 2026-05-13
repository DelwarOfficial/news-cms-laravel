@extends('admin.layouts.app')
@section('title', 'Advertisements')
@section('page-title', 'Advertisements')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('admin.advertisements.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
        <i class="fas fa-plus"></i> Add Advertisement
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Title</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Position</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Type</th>
                <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($ads as $ad)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $ad->title }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $ad->position }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ ucfirst($ad->type) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $ad->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $ad->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.advertisements.edit', $ad) }}" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.advertisements.destroy', $ad) }}" class="inline" onsubmit="return confirm('Delete this advertisement?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No advertisements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if(method_exists($ads, 'links'))
        <div class="border-t border-gray-100 px-6 py-4">{{ $ads->links() }}</div>
    @endif
</div>
@endsection
