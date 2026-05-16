<?php

return [
    /*
    | Supported providers: 'deepseek', 'openai', 'grok', 'claude', 'gemini'
    */
    'provider' => env('AI_PROVIDER', 'deepseek'),

    'api_key' => env('AI_API_KEY', ''),
    'claude_api_key' => env('AI_CLAUDE_API_KEY', ''),
    'gemini_api_key' => env('AI_GEMINI_API_KEY', ''),

    'endpoints' => [
        'deepseek' => 'https://api.deepseek.com/v1/chat/completions',
        'openai'   => 'https://api.openai.com/v1/chat/completions',
        'grok'     => 'https://api.x.ai/v1/chat/completions',
        'claude'   => 'https://api.anthropic.com/v1/messages',
        'gemini'   => 'https://generativelanguage.googleapis.com/v1beta/models',
    ],

    'models' => [
        'deepseek' => 'deepseek-chat',
        'openai'   => 'gpt-4o-mini',
        'grok'     => 'grok-2-latest',
        'claude'   => 'claude-sonnet-4-20250514',
        'gemini'   => 'gemini-2.0-flash',
    ],
];
