<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'users' => ['index' => true, 'nullable' => true],
        'posts' => ['index' => true, 'nullable' => true],
        'categories' => ['index' => true, 'nullable' => true],
        'tags' => ['index' => true, 'nullable' => true],
        'media' => ['index' => true, 'nullable' => true],
        'media_folders' => ['index' => true, 'nullable' => true],
        'comments' => ['index' => true, 'nullable' => true],
        'pages' => ['index' => true, 'nullable' => true],
        'menus' => ['index' => true, 'nullable' => true],
        'menu_items' => ['index' => true, 'nullable' => true],
        'widgets' => ['index' => true, 'nullable' => true],
        'advertisements' => ['index' => true, 'nullable' => true],
        'content_placements' => ['index' => true, 'nullable' => true],
        'settings' => ['index' => true, 'nullable' => true],
        'api_keys' => ['index' => true, 'nullable' => true],
        'post_categories' => ['index' => true, 'nullable' => true],
        'post_tags' => ['index' => true, 'nullable' => true],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $config) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($config, $table) {
                $col = 'tenant_id';

                if (! Schema::hasColumn($table, $col)) {
                    $blueprint->unsignedBigInteger($col)->nullable($config['nullable'] ?? true);
                }

                // Best-effort idempotency: in dev environments this migration may be re-run after partial failures.
                try {
                    $blueprint->foreign($col, "{$table}_tenant_id_foreign")
                        ->references('id')
                        ->on('tenants')
                        ->cascadeOnDelete();
                } catch (\Throwable) {
                    // Ignore if the FK already exists (or was created with a different name).
                }

                if ($config['index'] ?? false) {
                    try {
                        $blueprint->index($col, "{$table}_tenant_id_index");
                    } catch (\Throwable) {
                        // Ignore if index already exists.
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $config) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (! Schema::hasColumn($table, 'tenant_id')) {
                    return;
                }

                try {
                    $blueprint->dropForeign("{$table}_tenant_id_foreign");
                } catch (\Throwable) {
                    // Ignore if FK missing.
                }

                try {
                    $blueprint->dropIndex("{$table}_tenant_id_index");
                } catch (\Throwable) {
                    // Ignore if index missing.
                }

                $blueprint->dropColumn('tenant_id');
            });
        }
    }
};
