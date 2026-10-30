<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('landing_pages', 'lp_content_language')) {
                $table->string('lp_content_language', 10)->default('en')->after('lp_product_kind');
            }
            if (!Schema::hasColumn('landing_pages', 'lp_target_country')) {
                $table->string('lp_target_country', 2)->nullable()->after('lp_content_language');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (Schema::hasColumn('landing_pages', 'lp_target_country')) {
                $table->dropColumn('lp_target_country');
            }
            if (Schema::hasColumn('landing_pages', 'lp_content_language')) {
                $table->dropColumn('lp_content_language');
            }
        });
    }
};
