@extends('admin.layouts.app')

@section('title', 'Edit Prompt')
@section('header', 'Edit Prompt: ' . $prompt->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.translation.prompts.update', $prompt) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Prompt Template <span class="text-red-500">*</span></label>
                <textarea name="prompt_template" rows="15"
                          class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm font-mono">{{ old('prompt_template', $prompt->prompt_template) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Use <code>{from}</code>, <code>{to}</code>, <code>{target}</code>, <code>{title}</code>, <code>{summary}</code>, <code>{body}</code>, <code>{meta_title}</code>, <code>{meta_description}</code> variables.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <input type="text" name="description" value="{{ old('description', $prompt->description) }}" maxlength="500"
                       class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm"
                       placeholder="Brief description of when this prompt is used">
            </div>

            <div>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $prompt->is_active))
                           class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.translation.prompts.index') }}" class="btn-secondary px-4 py-2.5 text-sm">Cancel</a>
                <button type="submit" class="btn-primary px-6 py-2.5 text-sm font-semibold">
                    <i class="fas fa-save mr-2"></i> Update Prompt
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
