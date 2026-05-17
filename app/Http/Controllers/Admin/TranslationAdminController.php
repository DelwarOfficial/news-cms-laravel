<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BulkTranslateJob;
use App\Jobs\TranslatePostJob;
use App\Models\AiProvider;
use App\Models\Post;
use App\Models\Setting;
use App\Models\TranslationLog;
use App\Models\TranslationPrompt;
use App\Models\TranslationUsage;
use App\Services\AiTranslatorService;
use App\Services\GoogleTranslateService;
use App\Support\FrontendCache;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;

class TranslationAdminController extends Controller
{
    public function __construct(
        private readonly AiTranslatorService $ai,
        private readonly GoogleTranslateService $google,
    ) {
    }

    public function settings()
    {
        $monthlyCost = TranslationLog::completed()
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('cost_usd');

        $monthlyCostLimit = (float) (config('translation.monthly_cost_limit') ?? 0);
        $monthlyCostPercent = $monthlyCostLimit > 0 ? ($monthlyCost / $monthlyCostLimit) * 100 : 0;

        $monthlyChars = TranslationUsage::query()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'completed')
            ->sum('character_count');

        $totalJobs = TranslationLog::where('created_at', '>=', now()->subDays(30))->count();
        $failedJobs = TranslationLog::where('created_at', '>=', now()->subDays(30))->where('status', 'failed')->count();

        $costByProvider = TranslationLog::completed()
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw("provider_name, SUM(cost_usd) as total_cost, COUNT(*) as total")
            ->groupBy('provider_name')
            ->orderByDesc('total_cost')
            ->get();

        $maxCost = $costByProvider->max('total_cost') ?? 0;

        $recentLogs = TranslationLog::latest()->take(15)->get();

        $activeProviders = AiProvider::active()->count();
        $activePrompts = TranslationPrompt::where('is_active', true)->count();

        return view('admin.translations.settings', compact(
            'monthlyCost', 'monthlyCostLimit', 'monthlyCostPercent',
            'monthlyChars', 'totalJobs', 'failedJobs',
            'costByProvider', 'maxCost', 'recentLogs',
            'activeProviders', 'activePrompts',
        ));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:deepseek,openai,grok,claude,gemini',
            'api_key' => 'nullable|string|max:255',
            'claude_api_key' => 'nullable|string|max:255',
            'gemini_api_key' => 'nullable|string|max:255',
            'google_project_id' => 'nullable|string|max:255',
            'google_key_path' => 'nullable|string|max:500',
            'monthly_limit' => 'nullable|integer|min:0',
        ]);

        $this->saveSetting('ai.provider', $data['provider']);

        if (! empty($data['api_key'])) {
            $this->saveSettingEncrypted('ai.api_key', $data['api_key']);
        }
        if (! empty($data['claude_api_key'])) {
            $this->saveSettingEncrypted('ai.claude_api_key', $data['claude_api_key']);
        }
        if (! empty($data['gemini_api_key'])) {
            $this->saveSettingEncrypted('ai.gemini_api_key', $data['gemini_api_key']);
        }
        if (! empty($data['google_project_id'])) {
            $this->saveSetting('google_translate.project_id', $data['google_project_id']);
        }
        if (! empty($data['google_key_path'])) {
            $this->saveSetting('google_translate.key_file_path', $data['google_key_path']);
        }
        if (isset($data['monthly_limit'])) {
            $this->saveSetting('google_translate.monthly_limit', (string) $data['monthly_limit']);
        }

        return redirect()->route('admin.translations.settings')
            ->with('success', 'Translation settings updated successfully.');
    }

    public function bulkTranslateForm()
    {
        $posts = Post::query()
            ->published()
            ->whereNull('title_en')
            ->orWhere('title_en', '')
            ->orderByDesc('published_at')
            ->take(50)
            ->get(['id', 'title', 'title_bn', 'title_en', 'published_at']);

        $recentlyTranslated = TranslationUsage::query()
            ->with('post:id,title,title_bn')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.translations.bulk', compact('posts', 'recentlyTranslated'));
    }

    public function bulkTranslate(Request $request)
    {
        $data = $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'integer|exists:posts,id',
            'from' => 'required|in:bn,en',
            'to' => 'required|in:bn,en',
            'method' => 'required|in:ai,google,ai_then_google',
        ]);

        BulkTranslateJob::dispatch(
            $data['post_ids'],
            $data['from'],
            $data['to'],
            $data['method'],
        );

        FrontendCache::flushContent();

        return redirect()->route('admin.translations.bulk')
            ->with('success', "{$count} posts processed for translation.");
    }

    public function singleTranslate(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'nullable|integer|exists:posts,id',
            'title_bn' => 'nullable|string|max:500',
            'summary_bn' => 'nullable|string|max:5000',
            'body_bn' => 'nullable|string',
            'meta_title_bn' => 'nullable|string|max:70',
            'meta_description_bn' => 'nullable|string|max:170',
            'method' => 'required|in:ai,google,ai_then_google',
        ]);

        if (! empty($data['post_id'])) {
            $post = Post::findOrFail($data['post_id']);

            if ($data['method'] === 'ai_then_google') {
                TranslatePostJob::dispatch($post);
                return response()->json(['success' => true, 'message' => 'Translation job queued.']);
            }

            $translated = $data['method'] === 'ai'
                ? $this->ai->translatePost($post)
                : $this->google->translatePost($post);

            if (empty($translated)) {
                return response()->json(['error' => 'Translation failed or returned empty'], 422);
            }

            $post->update($this->sanitizeTranslatedFields($translated));
            FrontendCache::flushContent();

            return response()->json(['success' => true] + $this->sanitizeTranslatedFields($translated));
        }

        $promptData = [
            'title_bn' => $data['title_bn'] ?? '',
            'summary_bn' => $data['summary_bn'] ?? '',
            'body_bn' => $data['body_bn'] ?? '',
            'meta_title_bn' => $data['meta_title_bn'] ?? '',
            'meta_description_bn' => $data['meta_description_bn'] ?? '',
        ];

        $result = $this->ai->translatePost(
            (new Post())->forceFill($promptData)
        );

        return response()->json($result ?: [
            'title_en' => '[EN] ' . ($data['title_bn'] ?? ''),
            'summary_en' => '[EN] ' . ($data['summary_bn'] ?? ''),
        ]);
    }

    public function usage()
    {
        $logs = TranslationLog::latest()->paginate(50);

        $totalCost = TranslationLog::completed()->sum('cost_usd');
        $totalChars = TranslationLog::completed()->sum('total_chars');
        $totalJobs = TranslationLog::count();
        $completedJobs = TranslationLog::completed()->count();
        $failedJobs = TranslationLog::failed()->count();

        $costByProvider = TranslationLog::completed()
            ->selectRaw("provider_name, SUM(cost_usd) as total_cost, COUNT(*) as total, SUM(input_tokens + output_tokens) as total_tokens, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->groupBy('provider_name')
            ->orderByDesc('total_cost')
            ->get();

        return view('admin.translations.usage', compact(
            'logs', 'totalCost', 'totalChars', 'totalJobs',
            'completedJobs', 'failedJobs', 'costByProvider',
        ));
    }

    private function saveSetting(string $key, string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'translation']);
    }

    private function sanitizeTranslatedFields(array $translated): array
    {
        foreach (['body_en', 'body_bn', 'summary_en', 'summary_bn'] as $field) {
            if (isset($translated[$field]) && is_string($translated[$field])) {
                $translated[$field] = app(RichTextSanitizer::class)->sanitize($translated[$field]);
            }
        }

        return $translated;
    }

    private function saveSettingEncrypted(string $key, string $value): void
    {
        Setting::updateOrCreate(['key' => $key], [
            'value' => encrypt($value),
            'group' => 'translation',
        ]);
    }
}
