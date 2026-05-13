<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts') || ! Schema::hasColumn('posts', 'is_photocard')) {
            return;
        }

        $hasPhotocards = DB::table('posts')
            ->where('is_photocard', true)
            ->exists();

        if ($hasPhotocards) {
            return;
        }

        $postIds = DB::table('posts')
            ->where('status', 'published')
            ->where(function ($query) {
                $query
                    ->whereNotNull('featured_image')
                    ->orWhereNotNull('image_path')
                    ->orWhereNotNull('featured_media_id')
                    ->orWhereNotNull('og_image');
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(10)
            ->pluck('id');

        if ($postIds->isEmpty()) {
            return;
        }

        DB::table('posts')
            ->whereIn('id', $postIds)
            ->update(['is_photocard' => true]);
    }

    public function down(): void
    {
        // Keep editorial choices intact if this migration is rolled back.
    }
};
