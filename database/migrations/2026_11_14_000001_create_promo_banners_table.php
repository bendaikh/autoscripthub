<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Single-row table holding the home page promotion banner
 * (Black Friday, Cyber Monday, etc.), edited from Admin > Promo Banner.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('promo_banners')) {
            return;
        }

        Schema::create('promo_banners', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('enabled')->default(0);
            $table->string('badge', 60)->nullable();
            $table->string('heading', 191)->nullable();
            $table->string('discount_text', 120)->nullable();
            $table->string('subheading', 500)->nullable();
            $table->string('coupon_code', 60)->nullable();
            $table->string('button_text', 60)->nullable();
            $table->string('button_url', 500)->nullable();
            $table->dateTime('ends_at')->nullable();        // stored in UTC
            $table->string('timezone', 64)->default('UTC'); // zone the admin entered the date in
            $table->timestamps();
        });

        DB::table('promo_banners')->insert([
            'id' => 1,
            'enabled' => 1,
            'badge' => 'Black Friday',
            'heading' => 'Black Friday Sale',
            'discount_text' => 'Huge Discounts',
            'subheading' => 'Our biggest sale of the year on every script, theme and tool. Limited time only!',
            'coupon_code' => null,
            'button_text' => 'Shop the Sale',
            'button_url' => '/shop',
            'ends_at' => '2026-11-30 23:59:59',
            'timezone' => 'UTC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_banners');
    }
};
