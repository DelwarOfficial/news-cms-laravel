@extends('admin.layouts.app')
@section('title', 'Members')
@section('page-title', 'Members')

@section('header-actions')
    <a href="{{ route('admin.members.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-plus"></i> New Member
    </a>
@endsection

@section('content')
<div class="mb-6 flex flex-col lg:flex-row lg:items-center gap-3 justify-between">
    <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-wrap items-center gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search members..." class="w-full sm:w-72 border border-gray-200 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <select name="role" class="border border-gray-200 px-4 py-2.5 rounded-xl text-sm bg-white">
            <option value="">All editorial roles</option>
            @foreach($roles as $roleName)
                <option value="{{ $roleName }}" @selected($role === $roleName)>{{ $roleName }}</option>
            @endforeach
        </select>
        <input type="hidden" name="sort_by" value="{{ $sortBy }}">
        <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
        <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 select-none">
            <tr>
                @include('admin.partials.sortable-th', ['column' => 'name', 'label' => 'Name', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                @include('admin.partials.sortable-th', ['column' => 'username', 'label' => 'Username', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                @include('admin.partials.sortable-th', ['column' => 'email', 'label' => 'Email', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Role</th>
                @include('admin.partials.sortable-th', ['column' => 'status', 'label' => 'Status', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                <th class="px-6 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($members as $member)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $member->name }}</div>
                    <div class="text-xs text-gray-400">{{ Str::limit($member->bio, 64) }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $member->username }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $member->email }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-medium">{{ $member->roles->first()?->name ?? 'Member' }}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : ($member->status === 'banned' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($member->status) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('admin.members.edit', $member) }}" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50">
                            <i class="fas fa-pencil text-xs"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Delete this member?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-8 py-12 text-center text-gray-500">No members yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($members->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $members->links() }}
        </div>
    @endif
</div>
@endsection
