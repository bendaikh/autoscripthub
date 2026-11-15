<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Manual crypto payments on landing pages (Admin > Landing Pages > Crypto Payments):
 * Binance Pay and KAST (QR code, ID/tag, pay link) and a USDT address any wallet can pay to.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crypto_pay_settings')) {
            Schema::create('crypto_pay_settings', function (Blueprint $table) {
                $table->unsignedInteger('id')->primary();
                $table->boolean('binance_enabled')->default(0);
                $table->string('binance_qr', 191)->nullable();       // file in public/storage/crypto
                $table->string('binance_pay_id', 64)->nullable();    // Binance Pay ID or nickname
                $table->string('binance_link', 255)->nullable();     // optional pay link
                $table->boolean('kast_enabled')->default(0);
                $table->string('kast_qr', 191)->nullable();
                $table->string('kast_tag', 64)->nullable();          // KAST tag, e.g. @name
                $table->string('kast_link', 255)->nullable();        // https://app.kast.xyz/kasttag/...
                $table->boolean('wallet_enabled')->default(0);       // pay to an address from any wallet
                $table->string('wallet_asset', 20)->nullable();      // e.g. USDT
                $table->string('wallet_network', 40)->nullable();    // e.g. Solana
                $table->string('wallet_address', 191)->nullable();
                $table->string('note', 500)->nullable();             // extra line for buyers
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_pay_settings');
    }
};
