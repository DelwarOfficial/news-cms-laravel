<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'idx_posts_status_date');
            $table->index(['is_breaking', 'published_at'], 'idx_posts_breaking');
            $table->index(['is_trending', 'view_count'], 'idx_posts_trending');
            $table->index(['primary_category_id', 'status'], 'idx_posts_category');
            $table->index(['division_id', 'status'], 'idx_posts_division');
            $table->index(['view_count'], 'idx_posts_views');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['slug', 'status'], 'idx_categories_slug');
        });

        Schema::table('content_placements', function (Blueprint $table) {
            $table->index(['placement_key', 'is_active'], 'idx_placement_active');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex('idx_posts_status_date');
                $table->dropIndex('idx_posts_breaking');
                $table->dropIndex('idx_posts_trending');
                $table->dropIndex('idx_posts_category');
                $table->dropIndex('idx_posts_division');
                $table->dropIndex('idx_posts_views');
            });

            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex('idx_categories_slug');
            });

            Schema::table('content_placements', function (Blueprint $table) {
                $table->dropIndex('idx_placement_active');
            });
        } catch (\Throwable) {
        }
    }
};
