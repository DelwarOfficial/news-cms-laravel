<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('upazilas')) {
            return;
        }

        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('name_bangla')->nullable();
            $table->string('code', 20)->nullable()->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['district_id', 'slug']);
            $table->index(['division_id', 'district_id']);
            $table->index(['district_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upazilas');
    }
};
