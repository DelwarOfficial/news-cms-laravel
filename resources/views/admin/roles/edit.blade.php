@extends('admin.layouts.app')
@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<form action="{{ route('admin.roles.update', $role) }}" method="POST" class="max-w-5xl space-y-6">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Role Name</label>
        <input type="text" name="name" value="{{ old('name', $role->name) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Permissions</h2>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($permissions as $group => $items)
                <div class="border border-gray-100 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-3 capitalize">{{ str_replace('_', ' ', $group) }}</h3>
                    <div class="space-y-2">
                        @foreach($items as $permission)
                            <label class="flex items-center gap-3 text-sm text-gray-700">
                                <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="rounded border-gray-300" @checked(in_array($permission, old('permissions', $selectedPermissions), true))>
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
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Save Changes</button>
    </div>
</form>
@endsection
