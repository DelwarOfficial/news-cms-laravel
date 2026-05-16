<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('media')) {
            return;
        }

        Schema::table('media', function (Blueprint $table) {
            // Rename old column names to new names (idempotent — checks before rename)
            if (Schema::hasColumn('media', 'filename') && !Schema::hasColumn('media', 'file_name')) {
                $table->renameColumn('filename', 'file_name');
            }
            if (Schema::hasColumn('media', 'original_name') && !Schema::hasColumn('media', 'name')) {
                $table->renameColumn('original_name', 'name');
            }
            if (Schema::hasColumn('media', 'path') && !Schema::hasColumn('media', 'file_path')) {
                $table->renameColumn('path', 'file_path');
            }
            if (Schema::hasColumn('media', 'url') && !Schema::hasColumn('media', 'file_url')) {
                $table->renameColumn('url', 'file_url');
            }
            if (Schema::hasColumn('media', 'mime_type') && !Schema::hasColumn('media', 'file_type')) {
                $table->renameColumn('mime_type', 'file_type');
            }
            if (Schema::hasColumn('media', 'size') && !Schema::hasColumn('media', 'file_size')) {
                $table->renameColumn('size', 'file_size');
            }

            // Add any CMS-expected columns that might be missing (e.g. from frontend-first schema)
            if (!Schema::hasColumn('media', 'name') && Schema::hasColumn('media', 'file_name')) {
                $table->string('name')->after('user_id');
            }
            if (!Schema::hasColumn('media', 'file_url')) {
                $table->string('file_url')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('media', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('media', 'folder_id')) {
                $table->foreignId('folder_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('media')) {
            return;
        }

        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'file_name') && !Schema::hasColumn('media', 'filename')) {
                $table->renameColumn('file_name', 'filename');
            }
            if (Schema::hasColumn('media', 'name') && !Schema::hasColumn('media', 'original_name')) {
                $table->renameColumn('name', 'original_name');
            }
            if (Schema::hasColumn('media', 'file_path') && !Schema::hasColumn('media', 'path')) {
                $table->renameColumn('file_path', 'path');
            }
            if (Schema::hasColumn('media', 'file_url') && !Schema::hasColumn('media', 'url')) {
                $table->renameColumn('file_url', 'url');
            }
            if (Schema::hasColumn('media', 'file_type') && !Schema::hasColumn('media', 'mime_type')) {
                $table->renameColumn('file_type', 'mime_type');
            }
            if (Schema::hasColumn('media', 'file_size') && !Schema::hasColumn('media', 'size')) {
                $table->renameColumn('file_size', 'size');
            }
        });
    }
};
