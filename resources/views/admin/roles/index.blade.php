@extends('admin.layouts.app')
@section('title', 'Roles')
@section('page-title', 'Roles')

@section('header-actions')
    @can('roles.create')
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
            <i class="fas fa-plus"></i> New Role
        </a>
    @endcan
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Role</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Permissions</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Guard</th>
                <th class="px-6 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($roles as $role)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $role->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $role->permissions_count }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $role->guard_name }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-lg hover:bg-blue-50">
                            <i class="fas fa-pencil text-xs"></i>
                        </a>
                        @if($role->name !== \App\Services\Rbac\PermissionService::ROLE_SUPER_ADMIN)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-8 py-12 text-center text-gray-500">No roles yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
