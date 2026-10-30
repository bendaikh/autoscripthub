<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_page_ad_costs')) {
            Schema::create('landing_page_ad_costs', function (Blueprint $table) {
                $table->id('lpac_id');
                $table->unsignedBigInteger('lp_id');
                $table->string('lpac_country_code', 2);
                $table->date('lpac_date');
                $table->decimal('lpac_amount', 12, 2);
                $table->string('lpac_currency', 3)->default('USD');
                $table->text('lpac_notes')->nullable();
                $table->timestamps();

                $table->index(['lp_id', 'lpac_date']);
                $table->index('lpac_country_code');
            });
        }

        if (!Schema::hasTable('landing_page_revenues')) {
            Schema::create('landing_page_revenues', function (Blueprint $table) {
                $table->id('lpr_id');
                $table->unsignedBigInteger('lp_id');
                $table->string('lpr_country_code', 2);
                $table->date('lpr_date');
                $table->decimal('lpr_amount', 12, 2);
                $table->unsignedInteger('lpr_quantity')->default(1);
                $table->string('lpr_currency', 3)->default('USD');
                $table->text('lpr_notes')->nullable();
                $table->timestamps();

                $table->index(['lp_id', 'lpr_date']);
                $table->index('lpr_country_code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_revenues');
        Schema::dropIfExists('landing_page_ad_costs');
    }
};
