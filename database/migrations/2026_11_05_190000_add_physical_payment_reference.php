<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('physical_orders') || Schema::hasColumn('physical_orders', 'payment_reference')) {
            return;
        }

        Schema::table('physical_orders', function (Blueprint $table) {
            $table->string('payment_reference', 191)->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('physical_orders') || !Schema::hasColumn('physical_orders', 'payment_reference')) {
            return;
        }

        Schema::table('physical_orders', function (Blueprint $table) {
            $table->dropColumn('payment_reference');
        });
    }
};
