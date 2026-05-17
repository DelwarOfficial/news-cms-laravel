<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function existingIndexes(string $table): array
    {
        try {
            if (DB::getDriverName() === 'sqlite') {
                $indexes = [];
                foreach (DB::select("PRAGMA index_list('{$table}')") as $row) {
                    $indexes[$row->name] = true;
                }
                return array_keys($indexes);
            }

            $indexes = [];
            foreach (DB::select("SHOW INDEX FROM {$table}") as $row) {
                $indexes[$row->Key_name] = true;
            }
            return array_keys($indexes);
        } catch (\Exception) {
            return [];
        }
    }

    public function up(): void
    {
        // =====================================================================
        // POSTS — composite indexes for common filtered listing queries
        // =====================================================================
        if (Schema::hasTable('posts')) {
            $existing = $this->existingIndexes('posts');

            Schema::table('posts', function (Blueprint $table) use ($existing) {
                // General published listing
                if (! in_array('posts_status_published_idx', $existing, true)
                    && Schema::hasColumn('posts', 'status')
                    && Schema::hasColumn('posts', 'published_at')) {
                    $table->index(['status', 'published_at'], 'posts_status_published_idx');
                }

                // Featured flag + status + published_at (homepage hero queries)
                if (! in_array('posts_featured_status_pub_idx', $existing, true)
                    && Schema::hasColumn('posts', 'is_featured')) {
                    $table->index(['is_featured', 'status', 'published_at'], 'posts_featured_status_pub_idx');
                }

                // Editors pick flag (single column)
                // NOTE: is_photocard already indexed by prior migration; is_breaking, is_featured, is_trending already indexed by 2026_05_15_000002
                if (! in_array('posts_is_editors_pick_index', $existing, true)
                    && Schema::hasColumn('posts', 'is_editors_pick')) {
                    $table->index('is_editors_pick');
                }

                // Editors pick + status + published_at
                if (! in_array('posts_editorspick_status_pub_idx', $existing, true)
                    && Schema::hasColumn('posts', 'is_editors_pick')) {
                    $table->index(['is_editors_pick', 'status', 'published_at'], 'posts_editorspick_status_pub_idx');
                }

                // Sticky flag
                if (! in_array('posts_is_sticky_index', $existing, true)
                    && Schema::hasColumn('posts', 'is_sticky')) {
                    $table->index('is_sticky');
                }

                // View count (popular sort)
                if (! in_array('posts_view_count_index', $existing, true)
                    && Schema::hasColumn('posts', 'view_count')) {
                    $table->index('view_count');
                }

                // User ID (author lookups)
                if (! in_array('posts_user_id_index', $existing, true)
                    && Schema::hasColumn('posts', 'user_id')) {
                    $table->index('user_id');
                }

                // Author ID
                if (! in_array('posts_author_id_index', $existing, true)
                    && Schema::hasColumn('posts', 'author_id')) {
                    $table->index('author_id');
                }
            });
        }

        // =====================================================================
        // CATEGORIES
        // =====================================================================
        if (Schema::hasTable('categories')) {
            $existing = $this->existingIndexes('categories');

            Schema::table('categories', function (Blueprint $table) use ($existing) {
                if (! in_array('categories_parent_id_index', $existing, true)
                    && Schema::hasColumn('categories', 'parent_id')) {
                    $table->index('parent_id');
                }
                if (! in_array('categories_status_index', $existing, true)
                    && Schema::hasColumn('categories', 'status')) {
                    $table->index('status');
                }
                if (! in_array('categories_order_index', $existing, true)
                    && Schema::hasColumn('categories', 'order')) {
                    $table->index('order');
                }
            });
        }

        // =====================================================================
        // TAGS
        // =====================================================================
        if (Schema::hasTable('tags')) {
            $existing = $this->existingIndexes('tags');

            Schema::table('tags', function (Blueprint $table) use ($existing) {
                if (! in_array('tags_status_index', $existing, true)
                    && Schema::hasColumn('tags', 'status')) {
                    $table->index('status');
                }
                if (! in_array('tags_name_index', $existing, true)
                    && Schema::hasColumn('tags', 'name')) {
                    $table->index('name');
                }
            });
        }

        // =====================================================================
        // PIVOT TABLES — reverse lookup indexes (category_id / tag_id alone)
        // =====================================================================
        if (Schema::hasTable('post_categories')) {
            $existing = $this->existingIndexes('post_categories');

            Schema::table('post_categories', function (Blueprint $table) use ($existing) {
                if (! in_array('post_categories_category_id_index', $existing, true)) {
                    $table->index('category_id');
                }
            });
        }

        if (Schema::hasTable('post_tags')) {
            $existing = $this->existingIndexes('post_tags');

            Schema::table('post_tags', function (Blueprint $table) use ($existing) {
                if (! in_array('post_tags_tag_id_index', $existing, true)) {
                    $table->index('tag_id');
                }
            });
        }

        // =====================================================================
        // PAGES
        // =====================================================================
        if (Schema::hasTable('pages')) {
            $existing = $this->existingIndexes('pages');

            Schema::table('pages', function (Blueprint $table) use ($existing) {
                if (! in_array('pages_status_order_idx', $existing, true)
                    && Schema::hasColumn('pages', 'status')
                    && Schema::hasColumn('pages', 'order')) {
                    $table->index(['status', 'order'], 'pages_status_order_idx');
                }
            });
        }
    }

    public function down(): void
    {
        $indexes = [
            'posts' => [
                'posts_status_published_idx',
                'posts_featured_status_pub_idx',
                'posts_is_editors_pick_index',
                'posts_editorspick_status_pub_idx',
                'posts_is_sticky_index',
                'posts_view_count_index',
                'posts_user_id_index',
                'posts_author_id_index',
            ],
            'categories' => [
                'categories_parent_id_index',
                'categories_status_index',
                'categories_order_index',
            ],
            'tags' => [
                'tags_status_index',
                'tags_name_index',
            ],
            'post_categories' => [
                'post_categories_category_id_index',
            ],
            'post_tags' => [
                'post_tags_tag_id_index',
            ],
            'pages' => [
                'pages_status_order_idx',
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) use ($tableIndexes) {
                foreach ($tableIndexes as $index) {
                    try {
                        $table->dropIndex($index);
                    } catch (\Exception) {
                        // Index may not exist
                    }
                }
            });
        }
    }
};
