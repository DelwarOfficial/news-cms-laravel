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
            if (! Schema::hasColumn('posts', 'author_id')) {
                $table->foreignId('author_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('posts', 'shoulder')) {
                $table->string('shoulder', 255)->nullable()->after('upazila_id');
            }

            if (! Schema::hasColumn('posts', 'post_format')) {
                $table->string('post_format', 40)->default('standard')->after('shoulder')->index();
            }

            if (! Schema::hasColumn('posts', 'show_author')) {
                $table->boolean('show_author')->default(true)->after('allow_comments');
            }

            if (! Schema::hasColumn('posts', 'show_publish_date')) {
                $table->boolean('show_publish_date')->default(true)->after('show_author');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            foreach (['show_publish_date', 'show_author', 'post_format', 'shoulder'] as $column) {
                if (Schema::hasColumn('posts', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('posts', 'author_id')) {
                $table->dropConstrainedForeignId('author_id');
            }
        });
    }
};
