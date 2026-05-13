<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'raw_import_payload')) {
                $table->json('raw_import_payload')->nullable()->after('show_publish_date');
            }
            if (! Schema::hasColumn('posts', 'source_url')) {
                $table->string('source_url', 500)->nullable()->after('canonical_url');
            }
            if (! Schema::hasColumn('posts', 'source_name')) {
                $table->string('source_name', 255)->nullable()->after('source_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            foreach (['raw_import_payload', 'source_url', 'source_name'] as $col) {
                if (Schema::hasColumn('posts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
