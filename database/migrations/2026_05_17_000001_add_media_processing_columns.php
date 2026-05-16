<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (! Schema::hasColumn('media', 'file_path_webp')) {
                $table->string('file_path_webp')->nullable()->after('file_path');
            }
            if (! Schema::hasColumn('media', 'thumbnails')) {
                $table->json('thumbnails')->nullable()->after('file_path_webp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['file_path_webp', 'thumbnails']);
        });
    }
};
