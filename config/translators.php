<?php

use App\Services\Translation\Drivers\ClaudeTranslatorDriver;
use App\Services\Translation\Drivers\GeminiTranslatorDriver;
use App\Services\Translation\Drivers\OpenAiCompatibleTranslatorDriver;

return [
    'default' => env('TRANSLATOR_PROVIDER', env('AI_PROVIDER', 'deepseek')),

    'timeout' => (int) env('TRANSLATOR_TIMEOUT', 60),
    'temperature' => (float) env('TRANSLATOR_TEMPERATURE', 0.3),
    'max_tokens' => (int) env('TRANSLATOR_MAX_TOKENS', 8192),

    'providers' => [
        'openai' => [
            'driver' => OpenAiCompatibleTranslatorDriver::class,
            'api_key' => env('OPENAI_API_KEY', env('AI_API_KEY', '')),
            'endpoint' => env('OPENAI_TRANSLATOR_ENDPOINT', 'https://api.openai.com/v1/chat/completions'),
            'model' => env('OPENAI_TRANSLATOR_MODEL', 'gpt-4o-mini'),
        ],

        'claude' => [
            'driver' => ClaudeTranslatorDriver::class,
            'api_key' => env('ANTHROPIC_API_KEY', env('AI_CLAUDE_API_KEY', env('AI_API_KEY', ''))),
            'endpoint' => env('CLAUDE_TRANSLATOR_ENDPOINT', 'https://api.anthropic.com/v1/messages'),
            'model' => env('CLAUDE_TRANSLATOR_MODEL', 'claude-sonnet-4-20250514'),
        ],

        'gemini' => [
            'driver' => GeminiTranslatorDriver::class,
            'api_key' => env('GEMINI_API_KEY', env('AI_GEMINI_API_KEY', env('AI_API_KEY', ''))),
            'endpoint' => env('GEMINI_TRANSLATOR_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models'),
            'model' => env('GEMINI_TRANSLATOR_MODEL', 'gemini-2.0-flash'),
        ],

        'deepseek' => [
            'driver' => OpenAiCompatibleTranslatorDriver::class,
            'api_key' => env('DEEPSEEK_API_KEY', env('AI_API_KEY', '')),
            'endpoint' => env('DEEPSEEK_TRANSLATOR_ENDPOINT', 'https://api.deepseek.com/v1/chat/completions'),
            'model' => env('DEEPSEEK_TRANSLATOR_MODEL', 'deepseek-chat'),
        ],
    ],

    'prompts' => [
        'post' => <<<'PROMPT'
Translate the following {from} news content to {to}. Preserve all HTML tags exactly as-is. Do not wrap the response in markdown code blocks. Return a JSON object with keys: title_{target}, summary_{target}, body_{target}, meta_title_{target}, meta_description_{target}.

---
title_{from}: {title}
summary_{from}: {summary}
body_{from}: {body}
meta_title_{from}: {meta_title}
meta_description_{from}: {meta_description}
---
PROMPT,

        'text' => <<<'PROMPT'
Translate the following {from} text to {to}. Return only the translated text, no explanations.

{text}
PROMPT,
    ],
];
