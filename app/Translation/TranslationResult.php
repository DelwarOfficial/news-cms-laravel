<?php

namespace App\Translation;

readonly class TranslationResult
{
    public function __construct(
        public string  $content,
        public string  $provider,
        public string  $model,
        public int     $inputTokens = 0,
        public int     $outputTokens = 0,
        public float   $costUsd = 0.0,
        public bool    $success = true,
        public ?string $error = null,
    ) {}
}
