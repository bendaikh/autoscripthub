<?php

use Fickrr\Support\OsLicenseCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the single-price toggle. Existing rows stay null (off) except the
     * named license parents, which are switched on.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('category', 'single_price')) {
            Schema::table('category', function (Blueprint $table) {
                $table->unsignedTinyInteger('single_price')->nullable();
            });
        }
        if (Schema::hasTable('subcategory') && ! Schema::hasColumn('subcategory', 'single_price')) {
            Schema::table('subcategory', function (Blueprint $table) {
                $table->unsignedTinyInteger('single_price')->nullable();
            });
        }

        $names = OsLicenseCatalog::FALLBACK_PARENT_NAMES;
        $parents = DB::table('category')->select('cat_id', 'category_name')->get();
        foreach ($parents as $parent) {
            if (! OsLicenseCatalog::isFallbackParentName($parent->category_name, $names)) {
                continue;
            }
            DB::table('category')->where('cat_id', $parent->cat_id)->update(['single_price' => 1]);
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasColumn('category', 'single_price')) {
            Schema::table('category', function (Blueprint $table) {
                $table->dropColumn('single_price');
            });
        }
        if (Schema::hasTable('subcategory') && Schema::hasColumn('subcategory', 'single_price')) {
            Schema::table('subcategory', function (Blueprint $table) {
                $table->dropColumn('single_price');
            });
        }
    }
};
