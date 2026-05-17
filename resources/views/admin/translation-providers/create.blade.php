@extends('admin.layouts.app')

@section('title', 'Add AI Provider')
@section('header', 'Add AI Provider')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.translation.providers.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm"
                       placeholder="deepseek, openai, claude, gemini">
                <p class="text-xs text-gray-400 mt-1">Unique identifier used in code (lowercase, no spaces).</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Driver Class <span class="text-red-500">*</span></label>
                <select name="driver_class" required
                        class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                    @foreach($driverOptions as $class => $label)
                        <option value="{{ $class }}" @selected(old('driver_class') === $class)>{{ $label }} ({{ class_basename($class) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">API Key</label>
                <input type="password" name="api_key" value="{{ old('api_key') }}"
                       class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm"
                       placeholder="sk-...">
                <p class="text-xs text-gray-400 mt-1">Leave empty to use the <code>.env</code> fallback key.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Model</label>
                    <input type="text" name="model" value="{{ old('model') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm"
                           placeholder="gpt-4o-mini, deepseek-chat, ...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Endpoint (optional)</label>
                    <input type="text" name="endpoint" value="{{ old('endpoint') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm"
                           placeholder="https://api.openai.com/v1/chat/completions">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="flex items-center gap-3 mt-6">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Options (JSON)</label>
                <textarea name="options" rows="3"
                          class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm font-mono"
                          placeholder='{"temperature": 0.3, "max_tokens": 8192}'>{{ old('options') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.translation.providers.index') }}" class="btn-secondary px-4 py-2.5 text-sm">Cancel</a>
                <button type="submit" class="btn-primary px-6 py-2.5 text-sm font-semibold">
                    <i class="fas fa-save mr-2"></i> Create Provider
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
