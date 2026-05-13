<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexName = 'posts_location_status_published_idx';

    public function up(): void
    {
        if (! Schema::hasTable('posts') || $this->hasIndex()) {
            return;
        }

        foreach (['division_id', 'district_id', 'upazila_id', 'status', 'published_at'] as $column) {
            if (! Schema::hasColumn('posts', $column)) {
                return;
            }
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->index(
                ['division_id', 'district_id', 'upazila_id', 'status', 'published_at'],
                $this->indexName
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts') || ! $this->hasIndex()) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex($this->indexName);
        });
    }

    private function hasIndex(): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return DB::table('sqlite_master')
                ->where('type', 'index')
                ->where('name', $this->indexName)
                ->exists();
        }

        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'posts')
            ->where('index_name', $this->indexName)
            ->exists();
    }
};
