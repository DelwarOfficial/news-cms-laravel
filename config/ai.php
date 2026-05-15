<?php

return [
    /*
    | Supported providers: 'deepseek', 'openai', 'grok'
    */
    'provider' => env('AI_PROVIDER', 'deepseek'),

    'api_key' => env('AI_API_KEY', ''),

    'endpoints' => [
        'deepseek' => 'https://api.deepseek.com/v1/chat/completions',
        'openai'   => 'https://api.openai.com/v1/chat/completions',
        'grok'     => 'https://api.x.ai/v1/chat/completions',
    ],

    'models' => [
        'deepseek' => 'deepseek-chat',
        'openai'   => 'gpt-4o-mini',
        'grok'     => 'grok-2-latest',
    ],
];
