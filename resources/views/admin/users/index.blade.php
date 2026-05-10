@extends('admin.layouts.app')
@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">User Management</h2>
        <p class="text-sm text-gray-500 mt-1">Create and manage admin users, roles, and account status.</p>
    </div>
    @can('create', \App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-semibold text-sm">
            <i class="fas fa-plus text-xs"></i>
            Create User
        </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100 select-none">
            <tr>
                @include('admin.partials.sortable-th', ['column' => 'name', 'label' => 'Name', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                @include('admin.partials.sortable-th', ['column' => 'username', 'label' => 'Username', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                @include('admin.partials.sortable-th', ['column' => 'email', 'label' => 'Email', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Roles</th>
                @include('admin.partials.sortable-th', ['column' => 'created_at', 'label' => 'Created', 'sortBy' => $sortBy, 'sortDirection' => $sortDirection])
                <th class="px-6 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $user->username }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($user->roles as $role)
                            <span class="text-xs px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-medium">{{ $role->name }}</span>
                        @empty
                            <span class="text-gray-400 text-xs">No role</span>
                        @endforelse
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at?->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex justify-end gap-2">
                        @can('update', $user)
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">Edit</a>
                        @endcan
                        @can('delete', $user)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs">Delete</button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-8 py-12 text-center text-gray-500">No users yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
