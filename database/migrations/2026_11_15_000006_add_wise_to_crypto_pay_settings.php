<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Wise as a manual payment method (Admin > Crypto Payments): QR code, Wisetag
 * and payment link, approved by the admin like the crypto methods.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crypto_pay_settings') && !Schema::hasColumn('crypto_pay_settings', 'wise_enabled')) {
            Schema::table('crypto_pay_settings', function (Blueprint $table) {
                $table->boolean('wise_enabled')->default(0);
                $table->string('wise_qr', 191)->nullable();
                $table->string('wise_tag', 64)->nullable();     // Wisetag, e.g. @name
                $table->string('wise_link', 255)->nullable();   // https://wise.com/pay/me/...
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('crypto_pay_settings', 'wise_enabled')) {
            Schema::table('crypto_pay_settings', function (Blueprint $table) {
                $table->dropColumn(['wise_enabled', 'wise_qr', 'wise_tag', 'wise_link']);
            });
        }
    }
};
