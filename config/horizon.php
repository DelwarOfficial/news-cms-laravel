<?php

use Illuminate\Support\Str;

return [
    'domain' => env('HORIZON_DOMAIN'),
    'path' => env('HORIZON_PATH', 'horizon'),
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'),
    'middleware' => ['web', 'auth', 'must.change.password'],
    'waits' => [
        'redis:default' => 60,
        'redis:publishing' => 60,
        'redis:media' => 60,
        'redis:translations' => 60,
    ],
    'trim' => [
        'recent' => 60,
        'completed' => 10000,
        'recent_failed' => 10000,
        'failed' => 10000,
        'monitored' => 10000,
    ],
    'silenced' => [
        \App\Jobs\ProcessMediaUpload::class,
    ],
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'publishing', 'media', 'translations', 'sitemap', 'horizon'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'maxProcesses' => 10,
                'maxTime' => 0,
                'maxJobs' => 0,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 120,
                'nice' => 0,
            ],
        ],
        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'publishing', 'media', 'translations', 'sitemap', 'horizon'],
                'balance' => 'simple',
                'maxProcesses' => 5,
                'maxTime' => 0,
                'maxJobs' => 0,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 120,
                'nice' => 0,
            ],
        ],
    ],
];
