<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('key_prefix', 10);
            $table->string('key_hash', 64);
            $table->json('scopes')->nullable();
            $table->unsignedSmallInteger('rate_limit')->default(60);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('key_hash');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
