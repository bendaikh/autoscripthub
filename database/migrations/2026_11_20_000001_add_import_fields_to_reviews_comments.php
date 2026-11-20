<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reviews and comments imported by the admin (Admin > Items > Import reviews):
 * they have no user account, so the reviewer name and source are stored here.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['item_ratings', 'item_comments'] as $t) {
            if (Schema::hasTable($t) && !Schema::hasColumn($t, 'import_name')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->string('import_name', 100)->nullable();
                    $table->string('import_source', 60)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['item_ratings', 'item_comments'] as $t) {
            if (Schema::hasColumn($t, 'import_name')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->dropColumn(['import_name', 'import_source']);
                });
            }
        }
    }
};
