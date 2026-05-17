@extends('admin.layouts.app')

@section('title', 'Translation Dashboard')
@section('header', 'Translation Dashboard')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Cost Dashboard Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Monthly Cost</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($monthlyCost, 4) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Monthly Characters</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($monthlyChars) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total Jobs (30d)</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJobs }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Failed Jobs (30d)</p>
            <p class="text-2xl font-bold text-red-500">{{ $failedJobs }}</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.translation.providers.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                <i class="fas fa-microchip"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">AI Providers</p>
                <p class="text-xs text-gray-400">{{ $activeProviders }} active providers</p>
            </div>
        </a>
        <a href="{{ route('admin.translation.prompts.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600">
                <i class="fas fa-pen"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Translation Prompts</p>
                <p class="text-xs text-gray-400">{{ $activePrompts }} active prompts</p>
            </div>
        </a>
        <a href="{{ route('admin.translations.usage') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                <i class="fas fa-chart-simple"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Usage Details</p>
                <p class="text-xs text-gray-400">Full translation log</p>
            </div>
        </a>
    </div>

    {{-- Cost by Provider --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Cost by Provider (This Month)</h3>
        @if($costByProvider->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No translation activity this month.</p>
        @else
            <div class="space-y-3">
                @foreach($costByProvider as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $stat->provider_name }}</span>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-400">{{ $stat->total }} jobs</span>
                            <span class="text-sm font-mono font-semibold text-gray-900 dark:text-white">${{ number_format($stat->total_cost, 6) }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $maxCost > 0 ? ($stat->total_cost / $maxCost) * 100 : 0 }}%"></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Monthly Cost Limit --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Monthly Cost Limit</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure in <code>.env</code>: <code>TRANSLATION_MONTHLY_COST_LIMIT=50.00</code></p>
        @if($monthlyCostLimit > 0)
            <div class="flex items-center gap-4">
                <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-{{ $monthlyCostPercent > 80 ? 'red' : ($monthlyCostPercent > 50 ? 'amber' : 'blue') }}-500 h-2.5 rounded-full" style="width: {{ min($monthlyCostPercent, 100) }}%"></div>
                </div>
                <span class="text-sm font-mono font-semibold text-gray-700 dark:text-gray-300">{{ number_format($monthlyCostPercent, 1) }}%</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">${{ number_format($monthlyCost, 2) }} / ${{ number_format($monthlyCostLimit, 2) }} used</p>
        @else
            <p class="text-sm text-gray-400">No monthly cost limit is set.</p>
        @endif
    </div>

    {{-- Recent Translation Activity --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h3>
            <a href="{{ route('admin.translations.usage') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                View All <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>

        @if($recentLogs->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No translation activity yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 font-semibold">Type</th>
                            <th class="pb-3 font-semibold">Direction</th>
                            <th class="pb-3 font-semibold">Provider</th>
                            <th class="pb-3 font-semibold">Tokens</th>
                            <th class="pb-3 font-semibold">Cost</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                <td class="py-2.5 text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                                <td class="py-2.5 text-xs font-mono text-gray-600">{{ class_basename($log->translatable_type) }}</td>
                                <td class="py-2.5 text-xs font-mono">{{ $log->from_locale }} → {{ $log->to_locale }}</td>
                                <td class="py-2.5 text-xs">{{ $log->provider_name }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($log->input_tokens + $log->output_tokens) }}</td>
                                <td class="py-2.5 font-mono text-xs">${{ number_format($log->cost_usd, 6) }}</td>
                                <td class="py-2.5">
                                    @if($log->status === 'completed')
                                        <span class="badge badge-green">Done</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge badge-red">Failed</span>
                                    @else
                                        <span class="badge badge-amber">{{ $log->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
