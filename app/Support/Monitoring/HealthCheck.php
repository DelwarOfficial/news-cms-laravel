<?php

namespace App\Support\Monitoring;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthCheck
{
    public static function run(): array
    {
        $dbOk = false;
        $redisOk = false;
        $errors = [];

        try {
            DB::select('select 1');
            $dbOk = true;
        } catch (\Throwable $e) {
            $errors['db'] = $e->getMessage();
        }

        $shouldCheckRedis = in_array(config('cache.default'), ['redis'], true)
            || in_array(config('queue.default'), ['redis'], true)
            || in_array(config('session.driver'), ['redis'], true);

        if (! $shouldCheckRedis) {
            $redisOk = true;
        } else {
            try {
                Redis::connection()->ping();
                $redisOk = true;
            } catch (\Throwable $e) {
                $errors['redis'] = $e->getMessage();
            }
        }

        return [
            'ok' => $dbOk && $redisOk,
            'checks' => [
                'db' => $dbOk,
                'redis' => $redisOk,
            ],
            'errors' => $errors,
        ];
    }
}
