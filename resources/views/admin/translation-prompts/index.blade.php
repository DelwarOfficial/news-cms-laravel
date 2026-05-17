@extends('admin.layouts.app')

@inject('str', 'Illuminate\Support\Str')

@section('title', 'Translation Prompts')
@section('header', 'Translation Prompts')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle text-green-500"></i><span>{{ session('success') }}</span></div>
    @endif

    <p class="text-sm text-gray-500 dark:text-gray-400">Edit the AI prompts used for news translation. Changes apply immediately to new translation jobs.</p>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="p-4 font-semibold">Name</th>
                    <th class="p-4 font-semibold">Description</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Preview</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prompts as $prompt)
                    <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-4 font-medium text-gray-900 dark:text-white">{{ $prompt->name }}</td>
                        <td class="p-4 text-gray-500 max-w-xs truncate">{{ $prompt->description ?: '—' }}</td>
                        <td class="p-4">
                            @if($prompt->is_active)
                                <span class="badge badge-green">Active</span>
                            @else
                                <span class="badge badge-red">Inactive</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <code class="text-xs text-gray-500 max-w-xs block truncate">{{ $str->limit(strip_tags($prompt->prompt_template), 60) }}</code>
                        </td>
                        <td class="p-4">
                            <a href="{{ route('admin.translation.prompts.edit', $prompt) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-sm text-gray-400">No prompts defined.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800">
        <p class="text-sm text-blue-700 dark:text-blue-300">
            <i class="fas fa-info-circle mr-1"></i>
            Available variables: <code>{from}</code>, <code>{to}</code>, <code>{target}</code>, <code>{title}</code>, <code>{summary}</code>, <code>{body}</code>, <code>{meta_title}</code>, <code>{meta_description}</code>
        </p>
    </div>
</div>
@endsection
