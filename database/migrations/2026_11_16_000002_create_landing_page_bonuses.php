<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Landing page bonuses: free bonus items (cover image + file or link) shown on
 * the landing page and delivered with the main product after payment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('landing_pages') && !Schema::hasColumn('landing_pages', 'bonus_section_enabled')) {
            Schema::table('landing_pages', function (Blueprint $table) {
                $table->boolean('bonus_section_enabled')->default(false);
                $table->string('bonus_section_title', 80)->nullable();
                $table->string('bonus_banner_image', 191)->nullable();
            });
        }

        if (!Schema::hasTable('landing_page_bonuses')) {
            Schema::create('landing_page_bonuses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('landing_page_id')->index();
                $table->string('title', 80);
                $table->string('description', 300);
                $table->string('image_path', 191)->nullable();          // public: public/storage/landing-bonuses
                $table->string('delivery_method', 10)->default('upload'); // upload | link
                $table->string('file_path', 191)->nullable();           // private: storage/app/landing-bonuses
                $table->string('file_original_name', 191)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();    // bytes
                $table->string('external_link', 500)->nullable();
                $table->string('value_label', 40)->nullable();
                $table->string('badge_text', 30)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            try {
                Schema::table('landing_page_bonuses', function (Blueprint $table) {
                    $table->foreign('landing_page_id')->references('lp_id')->on('landing_pages')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
                // the app also deletes bonus rows itself when a landing page is deleted
            }
        }

        // which bonuses each order received (shown in Admin > Landing Customers)
        if (Schema::hasTable('landing_page_orders') && !Schema::hasColumn('landing_page_orders', 'bonuses_delivered')) {
            Schema::table('landing_page_orders', function (Blueprint $table) {
                $table->text('bonuses_delivered')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_bonuses');
        if (Schema::hasColumn('landing_pages', 'bonus_section_enabled')) {
            Schema::table('landing_pages', function (Blueprint $table) {
                $table->dropColumn(['bonus_section_enabled', 'bonus_section_title', 'bonus_banner_image']);
            });
        }
        if (Schema::hasColumn('landing_page_orders', 'bonuses_delivered')) {
            Schema::table('landing_page_orders', function (Blueprint $table) {
                $table->dropColumn('bonuses_delivered');
            });
        }
    }
};
