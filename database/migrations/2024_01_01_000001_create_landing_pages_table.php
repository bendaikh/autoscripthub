<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->bigIncrements('lp_id');
            $table->string('lp_title');
            $table->string('lp_slug')->unique();
            $table->text('lp_description');
            $table->string('lp_banner_image')->nullable();
            $table->text('lp_features')->nullable(); // JSON encoded
            $table->decimal('lp_price', 10, 2)->default(0);
            $table->decimal('lp_extended_price', 10, 2)->nullable();
            $table->string('lp_currency', 10)->default('USD');
            $table->string('lp_meta_title')->nullable();
            $table->text('lp_meta_description')->nullable();
            $table->text('lp_meta_keywords')->nullable();
            $table->tinyInteger('lp_status')->default(1); // 1 = active, 0 = inactive
            $table->string('lp_product_id')->nullable(); // Link to existing product if needed
            $table->timestamps();
        });

        Schema::create('landing_page_gallery', function (Blueprint $table) {
            $table->bigIncrements('lpg_id');
            $table->unsignedBigInteger('lp_id');
            $table->string('lpg_image');
            $table->integer('lpg_order')->default(0);
            $table->timestamps();
            
            $table->foreign('lp_id')->references('lp_id')->on('landing_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_page_gallery');
        Schema::dropIfExists('landing_pages');
    }
}

