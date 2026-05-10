@extends('admin.layouts.app')
@section('title', 'New Role')
@section('page-title', 'New Role')

@section('content')
@php
    $allPermissions = collect($permissions)->flatten()->values()->all();
@endphp

<form
    action="{{ route('admin.roles.store') }}"
    method="POST"
    class="max-w-5xl space-y-6"
    x-data="rolePermissionBuilder(@js(old('permissions', [])))"
>
    @csrf

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Role Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Permissions</h2>
                <p class="text-sm text-gray-500 mt-1">Start from a base role template, then adjust individual permissions as needed.</p>
            </div>
            <div class="w-full lg:w-72">
                <label for="base_role" class="block text-sm font-semibold text-gray-700 mb-2">Base Role</label>
                <select
                    id="base_role"
                    x-model="baseRole"
                    x-on:change="applyTemplate"
                    class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white"
                >
                    <option value="">Choose template</option>
                    <option value="Editor">Editor</option>
                    <option value="Admin">Admin</option>
                    <option value="Author">Author/Reporter</option>
                </select>
            </div>
        </div>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($permissions as $group => $items)
                <div class="border border-gray-100 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-3 capitalize">{{ str_replace('_', ' ', $group) }}</h3>
                    <div class="space-y-2">
                        @foreach($items as $permission)
                            <label class="flex items-center gap-3 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission }}"
                                    class="rounded border-gray-300"
                                    x-model="selectedPermissions"
                                    @checked(in_array($permission, old('permissions', []), true))
                                >
                                <span>{{ $permission }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.roles.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Create Role</button>
    </div>
</form>

<script>
    function rolePermissionBuilder(initialPermissions = []) {
        const allPermissions = @json($allPermissions);
        const roleTemplates = {
            Editor: allPermissions.filter((permission) => {
                return permission.startsWith('posts.')
                    || ['categories.manage', 'tags.manage', 'comments.manage', 'media.manage'].includes(permission);
            }),
            Admin: allPermissions.filter((permission) => {
                return !['roles.manage', 'roles.create', 'api_keys.manage', 'settings.manage'].includes(permission);
            }),
            Author: [
                'posts.create',
                'posts.edit.own',
                'posts.submit_review',
                'posts.delete.own',
                'tags.manage',
                'media.manage',
                'dashboard.view',
            ].filter((permission) => allPermissions.includes(permission)),
        };

        return {
            baseRole: '',
            selectedPermissions: initialPermissions,
            templates: roleTemplates,
            applyTemplate() {
                if (!this.baseRole || !this.templates[this.baseRole]) {
                    return;
                }

                this.selectedPermissions = [...this.templates[this.baseRole]];
            },
        };
    }
</script>
@endsection
