<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('landing_pages', 'lp_ads_status')) {
                $table->string('lp_ads_status', 20)->default('off')->after('lp_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            if (Schema::hasColumn('landing_pages', 'lp_ads_status')) {
                $table->dropColumn('lp_ads_status');
            }
        });
    }
};
