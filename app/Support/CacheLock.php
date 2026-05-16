<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheLock
{
    public static function remember(string $key, int $ttl, callable $callback, int $lockTimeout = 10): mixed
    {
        $value = Cache::get($key);

        if ($value !== null) {
            return $value;
        }

        $lock = Cache::lock($key . ':lock', $lockTimeout);

        if ($lock->get()) {
            try {
                $value = Cache::get($key);
                if ($value !== null) {
                    return $value;
                }
                $value = $callback();
                Cache::put($key, $value, $ttl);
                return $value;
            } finally {
                $lock->release();
            }
        }

        $retries = 0;
        while ($retries < 5) {
            usleep(100_000);
            $value = Cache::get($key);
            if ($value !== null) {
                return $value;
            }
            $retries++;
        }

        return $callback();
    }

    public static function rememberWithStale(string $key, int $ttl, callable $callback, int $staleTtlMultiplier = 10, int $lockTimeout = 10): mixed
    {
        $value = Cache::get($key);

        if ($value !== null) {
            return $value;
        }

        $staleKey = $key . ':stale';
        $stale = Cache::get($staleKey);
        $lock = Cache::lock($key . ':lock', $lockTimeout);

        if ($lock->get()) {
            try {
                $value = Cache::get($key);
                if ($value !== null) {
                    return $value;
                }
                $value = $callback();
                Cache::put($key, $value, $ttl);
                Cache::put($staleKey, $value, $ttl * $staleTtlMultiplier);
                return $value;
            } finally {
                $lock->release();
            }
        }

        if ($stale !== null) {
            return $stale;
        }

        $retries = 0;
        while ($retries < 5) {
            usleep(100_000);
            $value = Cache::get($key);
            if ($value !== null) {
                return $value;
            }
            $retries++;
        }

        $value = $callback();
        Cache::put($key, $value, $ttl);
        Cache::put($staleKey, $value, $ttl * $staleTtlMultiplier);
        return $value;
    }
}
