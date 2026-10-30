<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_pages') || Schema::hasColumn('landing_pages', 'lp_about_description')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            $table->text('lp_about_description')->nullable()->after('lp_description');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('landing_pages') || !Schema::hasColumn('landing_pages', 'lp_about_description')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn('lp_about_description');
        });
    }
};
