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
            $table->string('lp_product_file')->nullable()->after('lp_product_id');
            $table->string('lp_product_file_type')->nullable()->after('lp_product_file'); // 'file' or 'folder'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['lp_product_file', 'lp_product_file_type']);
        });
    }
};
