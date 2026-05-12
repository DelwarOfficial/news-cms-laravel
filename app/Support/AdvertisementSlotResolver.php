<?php

namespace App\Support;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AdvertisementSlotResolver
{
    public static function resolve(string $name): array
    {
        $configured = config("ads.slots.{$name}", []);
        $advertisement = self::activeAdvertisement($name);

        if (! $advertisement) {
            return $configured;
        }

        return array_merge($configured, [
            'name' => $name,
            'label' => $advertisement->title ?: ($configured['label'] ?? config('ads.defaults.label')),
            'image_url' => $advertisement->type === 'image' ? $advertisement->imageUrl() : null,
            'target_url' => $advertisement->url,
            'html_code' => $advertisement->type === 'code' ? $advertisement->code : null,
            'is_active' => $advertisement->is_active,
        ]);
    }

    private static function activeAdvertisement(string $position): ?Advertisement
    {
        if (! config('ads.database.enabled', true)) {
            return null;
        }

        if (! Schema::hasTable('advertisements')) {
            return null;
        }

        $modelClass = config('ads.database.model', Advertisement::class);
        $cachePrefix = config('ads.database.cache_prefix', 'ads:slot:');

        return Cache::remember(
            "{$cachePrefix}{$position}",
            now()->addSeconds((int) config('homepage.cache.ttl', 300)),
            fn () => $modelClass::query()
                ->active()
                ->where('position', $position)
                ->latest('updated_at')
                ->first(),
        );
    }
}
