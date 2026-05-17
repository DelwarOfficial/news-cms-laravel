@extends('admin.layouts.app')

@section('title', 'Edit AI Provider')
@section('header', 'Edit AI Provider')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.translation.providers.update', $provider) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $provider->name) }}" required
                       class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Driver Class</label>
                <select name="driver_class" required
                        class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                    @foreach($driverOptions as $class => $label)
                        <option value="{{ $class }}" @selected(old('driver_class', $provider->driver_class) === $class)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">API Key</label>
                <input type="password" name="api_key" value="{{ old('api_key') }}"
                       class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm"
                       placeholder="Leave empty to keep existing or use .env fallback">
                @if($provider->api_key)
                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle"></i> Key is currently stored in database.</p>
                @else
                    <p class="text-xs text-amber-600 mt-1"><i class="fas fa-exclamation-triangle"></i> No key stored. Will use <code>.env</code> fallback.</p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Model</label>
                    <input type="text" name="model" value="{{ old('model', $provider->model) }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Endpoint</label>
                    <input type="text" name="endpoint" value="{{ old('endpoint', $provider->endpoint) }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $provider->sort_order) }}" min="0"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="flex items-center gap-3 mt-6">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $provider->is_active))
                               class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.translation.providers.index') }}" class="btn-secondary px-4 py-2.5 text-sm">Cancel</a>
                <button type="submit" class="btn-primary px-6 py-2.5 text-sm font-semibold">
                    <i class="fas fa-save mr-2"></i> Update Provider
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
