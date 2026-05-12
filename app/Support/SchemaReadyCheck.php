<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Post;

class SchemaReadyCheck
{
    public static function isPostsTableReady(): bool
    {
        return Cache::rememberForever('schema_posts_table_ready', function () {
            try {
                return class_exists(Post::class) && Schema::hasTable('posts');
            } catch (\Exception $e) {
                Log::error("Schema check failed for 'posts' table: " . $e->getMessage());
                return false;
            }
        });
    }

    public static function hasLocationColumns(): bool
    {
        return Cache::rememberForever('schema_location_columns_ready', function () {
            try {
                return (Schema::hasColumn('posts', 'division_id')
                        && Schema::hasColumn('posts', 'district_id')
                        && Schema::hasColumn('posts', 'upazila_id')
                        && Schema::hasTable('divisions')
                        && Schema::hasTable('districts')
                        && Schema::hasTable('upazilas'))
                    || (Schema::hasColumn('posts', 'division')
                        && Schema::hasColumn('posts', 'district')
                        && Schema::hasColumn('posts', 'upazila'));
            } catch (\Exception $e) {
                Log::error("Schema check failed for location columns: " . $e->getMessage());
                return false;
            }
        });
    }

    public static function hasLocalNewsColumns(): bool
    {
        return Cache::rememberForever('schema_local_news_columns_ready', function () {
            try {
                return self::isPostsTableReady()
                    && Schema::hasColumn('posts', 'division_id')
                    && Schema::hasColumn('posts', 'district_id')
                    && Schema::hasColumn('posts', 'upazila_id')
                    && Schema::hasTable('districts')
                    && Schema::hasTable('upazilas');
            } catch (\Exception $e) {
                Log::error("Schema check failed for local news columns: " . $e->getMessage());
                return false;
            }
        });
    }

    public static function hasSectionColumns(array $columns): bool
    {
        $key = 'schema_section_columns_' . md5(serialize($columns));
        return Cache::rememberForever($key, function () use ($columns) {
            try {
                if (!self::isPostsTableReady()) return false;
                foreach ($columns as $column) {
                    if (!Schema::hasColumn('posts', $column)) return false;
                }
                return true;
            } catch (\Exception $e) {
                Log::error("Schema check failed for section columns: " . $e->getMessage());
                return false;
            }
        });
    }
    
    public static function hasCategoryRelationship(): bool
    {
        return Cache::rememberForever('schema_category_relationship_ready', function () {
            try {
                return (Schema::hasTable('post_categories') || Schema::hasTable('post_category'))
                    && Schema::hasColumn('posts', 'primary_category_id');
            } catch (\Exception $e) {
                Log::error("Schema check failed for category relationships: " . $e->getMessage());
                return false;
            }
        });
    }

    public static function hasLocationIdColumns(): bool
    {
        return Cache::rememberForever('schema_location_id_columns_ready', function () {
            try {
                return Schema::hasColumn('posts', 'division_id')
                    && Schema::hasColumn('posts', 'district_id')
                    && Schema::hasColumn('posts', 'upazila_id')
                    && Schema::hasTable('divisions')
                    && Schema::hasTable('districts')
                    && Schema::hasTable('upazilas');
            } catch (\Exception $e) {
                Log::error("Schema check failed for location ID columns: " . $e->getMessage());
                return false;
            }
        });
    }
}
