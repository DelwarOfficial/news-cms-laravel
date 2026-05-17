<?php

namespace App\Services;

use App\Models\Post;
use App\Services\Translation\TranslatorManager;
use Illuminate\Support\Facades\Log;

class AiTranslatorService
{
    public function __construct(
        private readonly TranslatorManager $translators,
    ) {
    }

    public function translatePost(Post $post, string $from = 'bn', string $to = 'en'): array
    {
        $translated = $this->translators->translatePost($post, $from, $to);

        if (empty($translated)) {
            Log::warning('AI translation returned empty response for post', ['post_id' => $post->id]);
        }

        return $translated;
    }

    public function translateText(string $text, string $from = 'bn', string $to = 'en'): ?string
    {
        return $this->translators->translateText($text, $from, $to);
    }
}
