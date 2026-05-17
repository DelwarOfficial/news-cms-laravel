<?php

return [
    'default' => env('TRANSLATION_DEFAULT_DRIVER', 'deepseek'),

    'fallback_order' => ['deepseek', 'claude', 'openai', 'gemini'],

    'drivers' => [
        'claude' => [
            'key'   => env('CLAUDE_API_KEY'),
            'model' => env('CLAUDE_MODEL', 'claude-sonnet-4-20250514'),
        ],
        'openai' => [
            'key'   => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        ],
        'deepseek' => [
            'key'   => env('DEEPSEEK_API_KEY'),
            'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        ],
        'gemini' => [
            'key'   => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        ],
    ],

    'queue' => env('TRANSLATION_QUEUE', 'translations'),

    'auto_translate_on_publish' => (bool) env('AUTO_TRANSLATE', false),

    'monthly_cost_limit' => env('TRANSLATION_MONTHLY_COST_LIMIT'),
];
