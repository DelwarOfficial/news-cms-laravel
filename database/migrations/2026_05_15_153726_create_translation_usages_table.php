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
        Schema::create('translation_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_locale', 10)->default('bn');
            $table->string('to_locale', 10)->default('en');
            $table->integer('character_count')->default(0);
            $table->decimal('cost_estimate', 10, 6)->default(0);
            $table->string('status', 20)->default('completed'); // completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_usages');
    }
};
