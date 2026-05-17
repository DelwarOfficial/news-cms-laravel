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
            if (Schema::hasColumn('posts', 'division_id')) {
                $table->foreignId('division_id')
                    ->nullable()
                    ->change()
                    ->constrained('divisions')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('posts', 'district_id')) {
                $table->foreignId('district_id')
                    ->nullable()
                    ->change()
                    ->constrained('districts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'division_id')) {
                $table->dropForeign(['division_id']);
            }
            if (Schema::hasColumn('posts', 'district_id')) {
                $table->dropForeign(['district_id']);
            }
        });
    }
};
