<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('landing_pages', 'lp_source_url')) {
                $table->string('lp_source_url', 2048)->nullable()->after('lp_ads_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (Schema::hasColumn('landing_pages', 'lp_source_url')) {
                $table->dropColumn('lp_source_url');
            }
        });
    }
};
