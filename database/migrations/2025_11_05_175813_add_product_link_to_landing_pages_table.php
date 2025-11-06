<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->text('lp_product_link')->nullable()->after('lp_product_file_type');
            $table->string('lp_delivery_method')->default('upload')->after('lp_product_link'); // 'upload' or 'link'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['lp_product_link', 'lp_delivery_method']);
        });
    }
};
