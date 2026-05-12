<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $this->addBoolean($table, 'is_breaking', 'published_at');
            $this->addBoolean($table, 'is_featured', 'is_breaking');
            $this->addBoolean($table, 'is_sticky', 'is_featured');
            $this->addBoolean($table, 'is_trending', 'is_sticky');
            $this->addBoolean($table, 'is_editors_pick', 'is_trending');

            if (! Schema::hasColumn('posts', 'view_count')) {
                $table->unsignedBigInteger('view_count')->default(0)->after('status')->index();
            }

            if (! Schema::hasColumn('posts', 'reading_time')) {
                $table->unsignedSmallInteger('reading_time')->nullable()->after('view_count');
            }

            if (! Schema::hasColumn('posts', 'comment_count')) {
                $table->unsignedInteger('comment_count')->default(0)->after('reading_time');
            }

            if (! Schema::hasColumn('posts', 'featured_media_id')) {
                $table->foreignId('featured_media_id')
                    ->nullable()
                    ->after('featured_image')
                    ->constrained('media')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('posts', 'primary_category_id')) {
                $table->foreignId('primary_category_id')
                    ->nullable()
                    ->after('language_id')
                    ->constrained('categories')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('posts', 'division_id')) {
                $table->foreignId('division_id')
                    ->nullable()
                    ->after('primary_category_id')
                    ->constrained('divisions')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('posts', 'district_id')) {
                $table->foreignId('district_id')
                    ->nullable()
                    ->after('division_id')
                    ->constrained('districts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('posts', 'upazila_id')) {
                $table->foreignId('upazila_id')
                    ->nullable()
                    ->after('district_id')
                    ->constrained('upazilas')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('posts', 'image_path')) {
                $table->string('image_path')->nullable()->after('featured_media_id')->index();
            }

            if (! Schema::hasColumn('posts', 'featured_image_caption')) {
                $table->text('featured_image_caption')->nullable()->after('featured_image_alt');
            }

            if (! Schema::hasColumn('posts', 'featured_image_credit')) {
                $table->string('featured_image_credit')->nullable()->after('featured_image_caption');
            }

            if (! Schema::hasColumn('posts', 'featured_image_source')) {
                $table->string('featured_image_source')->nullable()->after('featured_image_credit');
            }

            if (! Schema::hasColumn('posts', 'featured_image_width')) {
                $table->unsignedInteger('featured_image_width')->nullable()->after('featured_image_source');
            }

            if (! Schema::hasColumn('posts', 'featured_image_height')) {
                $table->unsignedInteger('featured_image_height')->nullable()->after('featured_image_width');
            }

            if (! Schema::hasColumn('posts', 'og_image')) {
                $table->string('og_image')->nullable()->after('meta_description');
            }

            if (! Schema::hasColumn('posts', 'canonical_url')) {
                $table->string('canonical_url', 500)->nullable()->after('og_image');
            }

            if (! Schema::hasColumn('posts', 'allow_comments')) {
                $table->boolean('allow_comments')->default(true)->after('comment_count');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            foreach ([
                'upazila_id',
                'district_id',
                'division_id',
                'primary_category_id',
                'featured_media_id',
            ] as $column) {
                if (Schema::hasColumn('posts', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'featured_image_height',
                'featured_image_width',
                'featured_image_source',
                'featured_image_credit',
                'featured_image_caption',
                'image_path',
                'reading_time',
            ] as $column) {
                if (Schema::hasColumn('posts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addBoolean(Blueprint $table, string $column, string $after): void
    {
        if (! Schema::hasColumn('posts', $column)) {
            $table->boolean($column)->default(false)->after($after)->index();
        }
    }
};
