@extends('admin.layouts.app')
@section('title', 'Create API Key')
@section('page-title', 'Create API Key')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.api-keys.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Key Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required placeholder="e.g. Production CMS Sync">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Owner (optional)</label>
            <select name="user_id" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="">System (no owner)</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Scopes</label>
            <div class="grid grid-cols-2 gap-3">
                @foreach(['read' => 'Read content', 'write' => 'Write content', 'media' => 'Media access', 'cms' => 'CMS push/sync', 'admin' => 'Admin access'] as $value => $label)
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="scopes[]" value="{{ $value }}" @checked(in_array($value, old('scopes', ['read']))) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rate Limit (req/hour)</label>
                <input type="number" name="rate_limit" value="{{ old('rate_limit', 60) }}" min="1" max="10000" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Expires At (optional)</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.api-keys.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-semibold">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">Create API Key</button>
        </div>
    </form>
</div>
@endsection
