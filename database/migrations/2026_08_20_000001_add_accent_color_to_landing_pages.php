<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_pages') || Schema::hasColumn('landing_pages', 'lp_accent_color')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('lp_accent_color', 7)->nullable()->after('lp_cta_text');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('landing_pages') || !Schema::hasColumn('landing_pages', 'lp_accent_color')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('lp_accent_color');
        });
    }
};
