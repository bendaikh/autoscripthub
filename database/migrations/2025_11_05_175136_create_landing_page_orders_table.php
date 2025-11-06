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
        Schema::create('landing_page_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lp_id');
            $table->string('customer_email');
            $table->string('order_token')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10);
            $table->string('payment_method');
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->text('payment_details')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('lp_id')->references('lp_id')->on('landing_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_orders');
    }
};
