<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViewCounter
{
    private string $prefix = 'view_count:';
    private int $syncThreshold = 50;
    private array $localBuffer = [];

    public function increment(int $postId, int $amount = 1): void
    {
        $key = $this->prefix . $postId;

        Cache::increment($key, $amount);

        $this->localBuffer[$postId] = ($this->localBuffer[$postId] ?? 0) + $amount;

        if ($this->localBuffer[$postId] >= $this->syncThreshold) {
            $this->syncToDatabase($postId);
        }
    }

    public function get(int $postId): int
    {
        return (int) Cache::get($this->prefix . $postId, 0);
    }

    public function syncToDatabase(?int $postId = null): void
    {
        $keys = $postId !== null ? [$postId] : array_keys($this->localBuffer);
        if ($keys === []) return;

        foreach ($keys as $id) {
            try {
                $cached = Cache::get($this->prefix . $id, 0);
                if ($cached <= 0) continue;

                DB::table('posts')->where('id', $id)->increment('view_count', $cached);
                Cache::decrement($this->prefix . $id, $cached);
                unset($this->localBuffer[$id]);
            } catch (\Throwable $e) {
                Log::warning("ViewCounter sync failed for post {$id}: " . $e->getMessage());
            }
        }
    }

    public function flush(int $postId): void
    {
        $this->syncToDatabase($postId);
        Cache::forget($this->prefix . $postId);
    }

    public function syncAll(): void
    {
        $this->syncToDatabase();
    }

    public function getPopular(int $limit = 10, int $hours = 24): array
    {
        return Cache::remember("view_counter:popular:{$limit}:{$hours}", 300, function () use ($limit, $hours) {
            return DB::table('posts')
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->where('published_at', '>=', now()->subHours($hours))
                ->orderByDesc('view_count')
                ->take($limit)
                ->pluck('id')
                ->all();
        });
    }
}
