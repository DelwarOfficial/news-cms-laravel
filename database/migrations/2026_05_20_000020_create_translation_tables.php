<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('driver_class', 255);
            $table->text('api_key')->nullable();
            $table->string('endpoint', 500)->nullable();
            $table->string('model', 100)->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('translation_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable');
            $table->foreignId('provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $table->string('provider_name', 50);
            $table->string('model', 100)->nullable();
            $table->string('from_locale', 10);
            $table->string('to_locale', 10);
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->integer('total_chars')->default(0);
            $table->decimal('cost_usd', 12, 8)->default(0);
            $table->integer('duration_ms')->nullable();
            $table->string('status', 20)->default('completed');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['translatable_type', 'translatable_id']);
            $table->index('created_at');
            $table->index('provider_name');
            $table->index('status');
        });

        Schema::create('translation_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('prompt_template');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_logs');
        Schema::dropIfExists('ai_providers');
        Schema::dropIfExists('translation_prompts');
    }
};
