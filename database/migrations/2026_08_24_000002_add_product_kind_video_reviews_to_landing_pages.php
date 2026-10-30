<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_pages')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('landing_pages', 'lp_product_kind')) {
                $table->string('lp_product_kind', 20)->default('script')->after('lp_delivery_method');
            }
            if (!Schema::hasColumn('landing_pages', 'lp_product_video')) {
                $table->string('lp_product_video', 255)->nullable()->after('lp_banner_image');
            }
            if (!Schema::hasColumn('landing_pages', 'lp_reviews')) {
                $table->longText('lp_reviews')->nullable()->after('lp_features');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('landing_pages')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            $drops = [];
            foreach (['lp_product_kind', 'lp_product_video', 'lp_reviews'] as $col) {
                if (Schema::hasColumn('landing_pages', $col)) {
                    $drops[] = $col;
                }
            }
            if ($drops) {
                $table->dropColumn($drops);
            }
        });
    }
};
