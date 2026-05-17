<?php

namespace App\Services;

use App\Models\Post;
use App\Services\Translation\TranslationService;
use Illuminate\Support\Facades\Log;

class AiTranslatorService
{
    public function __construct(
        private readonly TranslationService $translation,
    ) {
    }

    public function translatePost(Post $post, string $from = 'bn', string $to = 'en'): array
    {
        $result = $this->translation->translatePost($post, $from, $to);

        $translated = $result['translated'] ?? [];

        if (empty($translated)) {
            Log::warning('AI translation returned empty response for post', ['post_id' => $post->id]);
            return [];
        }

        $suffixed = [];
        foreach ($translated as $key => $value) {
            $suffixed["{$key}_{$to}"] = $value;
        }

        return $suffixed;
    }

    public function translateText(string $text, string $from = 'bn', string $to = 'en'): ?string
    {
        return $this->translation->translateText($text, $from, $to);
    }
}
