<?php

namespace App\Translation;

use App\Translation\Contracts\TranslationProvider;
use App\Translation\Drivers\ClaudeTranslationDriver;
use App\Translation\Drivers\DeepSeekTranslationDriver;
use App\Translation\Drivers\GeminiTranslationDriver;
use App\Translation\Drivers\OpenAITranslationDriver;
use InvalidArgumentException;
use RuntimeException;

class TranslationManager
{
    private array $drivers = [];

    public function driver(?string $name = null): TranslationProvider
    {
        $name ??= config('translation.default', 'deepseek');

        return $this->drivers[$name] ??= match ($name) {
            'claude'   => new ClaudeTranslationDriver(),
            'openai'   => new OpenAITranslationDriver(),
            'deepseek' => new DeepSeekTranslationDriver(),
            'gemini'   => new GeminiTranslationDriver(),
            default    => throw new InvalidArgumentException("Unknown translation driver: {$name}"),
        };
    }

    public function translateWithFallback(string $text, string $from, string $to): TranslationResult
    {
        $priority = config('translation.fallback_order', ['deepseek', 'claude', 'openai', 'gemini']);

        foreach ($priority as $driver) {
            $d = $this->driver($driver);

            if (! $d->isAvailable()) {
                continue;
            }

            $result = $d->translate($text, $from, $to);

            if ($result->success) {
                return $result;
            }
        }

        throw new RuntimeException('All translation providers failed.');
    }
}
