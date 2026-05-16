<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->string('name_en', 255)->nullable()->after('name');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->string('name_en', 255)->nullable()->after('name');
        });

        Schema::table('upazilas', function (Blueprint $table) {
            $table->string('name_en', 255)->nullable()->after('name');
        });

        DB::statement('UPDATE divisions SET name_en = name WHERE name_en IS NULL');
        DB::statement('UPDATE districts SET name_en = name WHERE name_en IS NULL');
        DB::statement('UPDATE upazilas SET name_en = name WHERE name_en IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('upazilas', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });
    }
};
