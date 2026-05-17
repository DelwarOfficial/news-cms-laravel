<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\AiTranslatorService;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;

class PostTranslationController extends Controller
{
    public function __construct(
        private readonly AiTranslatorService $translator,
    ) {
    }

    public function translate(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'nullable|integer|exists:posts,id',
            'title_bn' => 'nullable|string|max:500',
            'summary_bn' => 'nullable|string|max:5000',
            'body_bn' => 'nullable|string',
            'meta_title_bn' => 'nullable|string|max:70',
            'meta_description_bn' => 'nullable|string|max:170',
        ]);

        // If a post_id is given, load the real post (ignore submitted text)
        if (! empty($data['post_id'])) {
            $post = Post::findOrFail($data['post_id']);
            $translated = $this->translator->translatePost($post);

            if (empty($translated)) {
                return response()->json(['error' => 'Translation failed or returned empty'], 422);
            }

            $post->update([
                'title_en' => $translated['title_en'] ?? $post->title_en,
                'summary_en' => $this->sanitizeTranslatedRichText($translated['summary_en'] ?? null),
                'body_en' => $this->sanitizeTranslatedRichText($translated['body_en'] ?? null),
                'meta_title_en' => $translated['meta_title_en'] ?? $post->meta_title_en,
                'meta_description_en' => $translated['meta_description_en'] ?? $post->meta_description_en,
            ]);

            \App\Support\FrontendCache::flushContent();

            return response()->json([
                'success' => true,
                'title_en' => $translated['title_en'] ?? '',
                'summary_en' => $translated['summary_en'] ?? '',
                'body_en' => $translated['body_en'] ?? '',
                'meta_title_en' => $translated['meta_title_en'] ?? '',
                'meta_description_en' => $translated['meta_description_en'] ?? '',
            ]);
        }

        // No post_id — translate submitted text on the fly (create form)
        $promptData = [
            'title_bn' => $data['title_bn'] ?? '',
            'summary_bn' => $data['summary_bn'] ?? '',
            'body_bn' => $data['body_bn'] ?? '',
            'meta_title_bn' => $data['meta_title_bn'] ?? '',
            'meta_description_bn' => $data['meta_description_bn'] ?? '',
        ];

        $response = $this->translator->translatePost(
            (new Post())->forceFill($promptData)
        );

        return response()->json($response ?: [
            'title_en' => '[EN] ' . ($data['title_bn'] ?? ''),
            'summary_en' => '[EN] ' . ($data['summary_bn'] ?? ''),
            'body_en' => '[EN] ' . ($data['body_bn'] ?? ''),
            'meta_title_en' => '[EN] ' . ($data['meta_title_bn'] ?? ''),
            'meta_description_en' => '[EN] ' . ($data['meta_description_en'] ?? ''),
        ]);
    }

    private function sanitizeTranslatedRichText(?string $value): ?string
    {
        return $value === null ? null : app(RichTextSanitizer::class)->sanitize($value);
    }
}
