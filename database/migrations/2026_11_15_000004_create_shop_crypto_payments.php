<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crypto payments (Binance Pay / KAST / USDT) made at the main site checkout.
 * One row per checkout; the order itself stays in the normal checkout tables
 * and is completed through Admin > Crypto Payments.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shop_crypto_payments')) {
            Schema::create('shop_crypto_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('purchase_token', 64)->index();     // checkout.purchase_token
                $table->char('token', 40)->unique();               // secret key in the buyer's payment link
                $table->string('method', 20);                      // binance | kast | usdt
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email', 191)->nullable();
                $table->string('status', 12)->default('pending')->index(); // pending | awaiting | completed | rejected
                $table->string('receipt', 191)->nullable();        // file in storage/app/crypto-receipts
                $table->string('reference', 150)->nullable();
                $table->string('payer', 100)->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_crypto_payments');
    }
};
