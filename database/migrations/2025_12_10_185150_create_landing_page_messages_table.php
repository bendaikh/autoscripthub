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
        Schema::create('landing_page_messages', function (Blueprint $table) {
            $table->bigIncrements('lpm_id');
            $table->unsignedBigInteger('lp_id');
            $table->string('lpm_name');
            $table->string('lpm_email');
            $table->string('lpm_phone')->nullable();
            $table->string('lpm_subject')->nullable();
            $table->text('lpm_message');
            $table->tinyInteger('lpm_status')->default(0); // 0 = unread, 1 = read, 2 = replied
            $table->timestamps();
            
            $table->foreign('lp_id')->references('lp_id')->on('landing_pages')->onDelete('cascade');
            $table->index('lpm_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_messages');
    }
};
