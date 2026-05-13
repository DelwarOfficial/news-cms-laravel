<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
                $table->id();
                $table->string('area', 100)->index();
                $table->string('type', 100)->index();
                $table->string('title');
                $table->text('content')->nullable();
                $table->json('config')->nullable();
                $table->unsignedSmallInteger('order')->default(0);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
