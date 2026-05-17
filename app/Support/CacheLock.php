<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheLock
{
    public static function remember(string $key, int $ttl, callable $callback, int $lockTimeout = 10, array $tags = []): mixed
    {
        $cache = self::cache($tags);
        $value = $cache->get($key);

        if ($value !== null) {
            return $value;
        }

        $lock = Cache::lock($key . ':lock', $lockTimeout);

        if ($lock->get()) {
            try {
                $value = $cache->get($key);
                if ($value !== null) {
                    return $value;
                }
                $value = $callback();
                $cache->put($key, $value, $ttl);
                return $value;
            } finally {
                $lock->release();
            }
        }

        $retries = 0;
        while ($retries < 5) {
            usleep(100_000);
            $value = $cache->get($key);
            if ($value !== null) {
                return $value;
            }
            $retries++;
        }

        return $callback();
    }

    public static function rememberWithStale(string $key, int $ttl, callable $callback, int $staleTtlMultiplier = 10, int $lockTimeout = 10, array $tags = []): mixed
    {
        $cache = self::cache($tags);
        $value = $cache->get($key);

        if ($value !== null) {
            return $value;
        }

        $staleKey = $key . ':stale';
        $stale = $cache->get($staleKey);
        $lock = Cache::lock($key . ':lock', $lockTimeout);

        if ($lock->get()) {
            try {
                $value = $cache->get($key);
                if ($value !== null) {
                    return $value;
                }
                $value = $callback();
                $cache->put($key, $value, $ttl);
                $cache->put($staleKey, $value, $ttl * $staleTtlMultiplier);
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
            $value = $cache->get($key);
            if ($value !== null) {
                return $value;
            }
            $retries++;
        }

        $value = $callback();
        $cache->put($key, $value, $ttl);
        $cache->put($staleKey, $value, $ttl * $staleTtlMultiplier);
        return $value;
    }

    private static function cache(array $tags)
    {
        if ($tags === []) {
            return Cache::store();
        }

        try {
            return Cache::tags($tags);
        } catch (\Throwable) {
            return Cache::store();
        }
    }
}
