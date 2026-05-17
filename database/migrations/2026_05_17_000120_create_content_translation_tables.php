<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('post_translations')) {
            Schema::create('post_translations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('title', 500)->nullable();
                $table->string('slug', 500)->nullable();
                $table->text('summary')->nullable();
                $table->longText('body')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();

                $table->unique(['post_id', 'locale']);
                $table->index(['locale', 'slug']);
            });
        }

        if (! Schema::hasTable('category_translations')) {
            Schema::create('category_translations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();

                $table->unique(['category_id', 'locale']);
                $table->index(['locale', 'slug']);
            });
        }

        if (! Schema::hasTable('tag_translations')) {
            Schema::create('tag_translations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();

                $table->unique(['tag_id', 'locale']);
                $table->index(['locale', 'slug']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translations');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('post_translations');
    }
};
