<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::whenTableHasColumn('categories', 'name', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_bn');
            $table->string('slug_bn')->nullable()->after('slug');
            $table->string('slug_en')->nullable()->after('slug_bn');
            $table->text('description_bn')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_bn');
            $table->string('meta_title_bn')->nullable()->after('meta_title');
            $table->string('meta_title_en')->nullable()->after('meta_title_bn');
            $table->text('meta_description_bn')->nullable()->after('meta_description');
            $table->text('meta_description_en')->nullable()->after('meta_description_bn');
        });

        Schema::whenTableHasColumn('tags', 'name', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_bn');
            $table->string('slug_bn')->nullable()->after('slug');
            $table->string('slug_en')->nullable()->after('slug_bn');
            $table->text('description_bn')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_bn');
            $table->string('meta_title_bn')->nullable()->after('meta_title');
            $table->string('meta_title_en')->nullable()->after('meta_title_bn');
            $table->text('meta_description_bn')->nullable()->after('meta_description');
            $table->text('meta_description_en')->nullable()->after('meta_description_bn');
        });

        Schema::whenTableHasColumn('pages', 'title', function (Blueprint $table) {
            $table->string('title_bn')->nullable()->after('title');
            $table->string('title_en')->nullable()->after('title_bn');
            $table->string('slug_bn')->nullable()->after('slug');
            $table->string('slug_en')->nullable()->after('slug_bn');
            $table->longText('content_bn')->nullable()->after('content');
            $table->longText('content_en')->nullable()->after('content_bn');
            $table->string('meta_title_bn')->nullable()->after('meta_title');
            $table->string('meta_title_en')->nullable()->after('meta_title_bn');
            $table->text('meta_description_bn')->nullable()->after('meta_description');
            $table->text('meta_description_en')->nullable()->after('meta_description_bn');
        });
    }

    public function down(): void
    {
        $drop = ['name_bn', 'name_en', 'slug_bn', 'slug_en', 'description_bn', 'description_en',
                 'meta_title_bn', 'meta_title_en', 'meta_description_bn', 'meta_description_en',
                 'content_bn', 'content_en', 'title_bn', 'title_en'];

        Schema::table('categories', fn (Blueprint $t) => collect($drop)->each(fn ($c) => $t->dropColumn($c)));
        Schema::table('tags', fn (Blueprint $t) => collect($drop)->each(fn ($c) => $t->dropColumn($c)));
        Schema::table('pages', fn (Blueprint $t) => collect($drop)->each(fn ($c) => $t->dropColumn($c)));
    }
};
