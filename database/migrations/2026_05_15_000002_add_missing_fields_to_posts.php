<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        $existingIndexes = $this->existingIndexes();

        Schema::table('posts', function (Blueprint $table) use ($existingIndexes) {
            // Individual indexes on flag columns
            if (!in_array('posts_is_breaking_index', $existingIndexes, true)) {
                $table->index('is_breaking');
            }
            if (!in_array('posts_is_featured_index', $existingIndexes, true)) {
                $table->index('is_featured');
            }
            if (!in_array('posts_is_trending_index', $existingIndexes, true)) {
                $table->index('is_trending');
            }

            // Compound index (status, published_at, is_breaking)
            $compoundIdx = 'posts_status_published_breaking_idx';
            if (!in_array($compoundIdx, $existingIndexes, true)) {
                $table->index(['status', 'published_at', 'is_breaking'], $compoundIdx);
            }

            // Ensure FK indexes exist (for columns added without FK constraints)
            if (!in_array('posts_featured_media_id_index', $existingIndexes, true)
                && in_array('posts_featured_media_id_foreign', $existingIndexes, true) === false) {
                try { Schema::getConnection()->getDoctrineColumn('posts', 'featured_media_id'); $table->index('featured_media_id'); } catch (\Exception) {}
            }
            if (!in_array('posts_primary_category_id_index', $existingIndexes, true)
                && in_array('posts_primary_category_id_foreign', $existingIndexes, true) === false) {
                try { Schema::getConnection()->getDoctrineColumn('posts', 'primary_category_id'); $table->index('primary_category_id'); } catch (\Exception) {}
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_published_breaking_idx');
            $table->dropIndex('posts_is_breaking_index');
            $table->dropIndex('posts_is_featured_index');
            $table->dropIndex('posts_is_trending_index');
        });
    }

    private function existingIndexes(): array
    {
        try {
            $indexes = [];
            foreach (DB::select('SHOW INDEX FROM posts') as $row) {
                $indexes[$row->Key_name] = true;
            }
            return array_keys($indexes);
        } catch (\Exception) {
            return [];
        }
    }
};
