@extends('admin.layouts.app')
@section('title', 'New Member')
@section('page-title', 'New Member')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.members.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                    @foreach($roles as $roleName)
                        <option value="{{ $roleName }}" @selected(old('role', 'Author/Reporter') === $roleName)>{{ $roleName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    <option value="banned" @selected(old('status') === 'banned')>Banned</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Avatar URL</label>
                <input type="url" name="avatar" value="{{ old('avatar') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Bio</label>
            <textarea name="bio" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">{{ old('bio') }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.members.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Create Member</button>
        </div>
    </form>
</div>
@endsection
