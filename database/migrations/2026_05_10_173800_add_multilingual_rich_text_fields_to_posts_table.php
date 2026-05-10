<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title_en', 500)->nullable()->after('title');
            $table->string('title_bn', 500)->nullable()->after('title_en');
            $table->string('slug_en', 500)->nullable()->after('slug');
            $table->string('slug_bn', 500)->nullable()->after('slug_en');
            $table->string('meta_title_en')->nullable()->after('meta_title');
            $table->string('meta_title_bn')->nullable()->after('meta_title_en');
            $table->text('meta_description_en')->nullable()->after('meta_description');
            $table->text('meta_description_bn')->nullable()->after('meta_description_en');
        });

        DB::table('posts')->orderBy('id')->lazy()->each(function ($post): void {
            DB::table('posts')->where('id', $post->id)->update([
                'title_en' => $post->title,
                'slug_en' => $post->slug,
                'meta_title_en' => $post->meta_title,
                'meta_description_en' => $post->meta_description,
            ]);

            $now = now();

            if (! empty($post->content)) {
                DB::table('rich_texts')->updateOrInsert(
                    [
                        'record_type' => (new Post())->getMorphClass(),
                        'record_id' => $post->id,
                        'field' => 'body_en',
                    ],
                    [
                        'body' => '<div>'.$post->content.'</div>',
                        'created_at' => $post->created_at ?? $now,
                        'updated_at' => $post->updated_at ?? $now,
                    ],
                );
            }

            if (! empty($post->excerpt)) {
                DB::table('rich_texts')->updateOrInsert(
                    [
                        'record_type' => (new Post())->getMorphClass(),
                        'record_id' => $post->id,
                        'field' => 'summary_en',
                    ],
                    [
                        'body' => '<div>'.$post->excerpt.'</div>',
                        'created_at' => $post->created_at ?? $now,
                        'updated_at' => $post->updated_at ?? $now,
                    ],
                );
            }
        });
    }

    public function down(): void
    {
        DB::table('rich_texts')->whereIn('field', [
            'body_en',
            'body_bn',
            'summary_en',
            'summary_bn',
        ])->delete();

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'title_en',
                'title_bn',
                'slug_en',
                'slug_bn',
                'meta_title_en',
                'meta_title_bn',
                'meta_description_en',
                'meta_description_bn',
            ]);
        });
    }
};
