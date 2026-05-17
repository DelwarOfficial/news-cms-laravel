<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\TranslatePostJob;
use App\Models\Post;
use App\Models\Setting;
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
        $providers = ['deepseek', 'openai', 'grok', 'claude', 'gemini'];
        $currentProvider = config('ai.provider', 'deepseek');
        $usage = TranslationUsage::query()
            ->selectRaw("DATE(created_at) as date, from_locale, to_locale, SUM(character_count) as total_chars, COUNT(*) as total_jobs, status")
            ->groupBy('date', 'from_locale', 'to_locale', 'status')
            ->orderByDesc('date')
            ->take(30)
            ->get();

        $monthlyChars = TranslationUsage::query()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'completed')
            ->sum('character_count');

        $monthlyLimit = (int) config('google_translate.monthly_limit', 0);

        return view('admin.translations.settings', compact(
            'providers', 'currentProvider', 'usage', 'monthlyChars', 'monthlyLimit'
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

        $count = 0;
        $posts = Post::whereIn('id', $data['post_ids'])->get();

        foreach ($posts as $post) {
            TranslatePostJob::dispatch(
                $post,
                $data['from'],
                $data['to'],
                $data['method'] === 'ai_then_google',
                $data['method'],
            );
            $count++;
        }

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

        // Live translate (no post_id - used in create form)
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
        $usage = TranslationUsage::query()
            ->with('post:id,title,title_bn')
            ->latest()
            ->paginate(50);

        $monthlyStats = TranslationUsage::query()
            ->selectRaw("from_locale, to_locale, status, SUM(character_count) as total_chars, COUNT(*) as total")
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('from_locale', 'to_locale', 'status')
            ->get();

        $totalMonthlyChars = $monthlyStats->where('status', 'completed')->sum('total_chars');

        return view('admin.translations.usage', compact('usage', 'monthlyStats', 'totalMonthlyChars'));
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
