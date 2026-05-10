@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                <input type="password" name="password" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                    <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
                    <option value="banned" @selected(old('status', $user->status) === 'banned')>Banned</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Avatar URL</label>
                <input type="url" name="avatar" value="{{ old('avatar', $user->avatar) }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Bio</label>
            <textarea name="bio" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Save Changes</button>
        </div>
    </form>
</div>
@endsection
