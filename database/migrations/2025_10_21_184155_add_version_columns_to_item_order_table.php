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
        Schema::table('item_order', function (Blueprint $table) {
            $table->string('purchased_version', 255)->nullable()->after('item_token');
            $table->text('purchased_version_url')->nullable()->after('purchased_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_order', function (Blueprint $table) {
            $table->dropColumn(['purchased_version', 'purchased_version_url']);
        });
    }
};
