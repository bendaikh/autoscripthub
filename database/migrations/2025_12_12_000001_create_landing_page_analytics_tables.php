<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingPageAnalyticsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_page_visits', function (Blueprint $table) {
            $table->bigIncrements('lpv_id');
            $table->unsignedBigInteger('lp_id');

            $table->string('lpv_visitor_id', 64)->index();
            $table->string('lpv_session_id', 255)->nullable()->index();
            $table->string('lpv_ip_hash', 64)->nullable()->index();

            $table->string('lpv_country_code', 2)->nullable()->index();

            $table->longText('lpv_user_agent')->nullable();
            $table->longText('lpv_referrer')->nullable();
            $table->longText('lpv_landing_url')->nullable();

            $table->string('lpv_utm_source')->nullable();
            $table->string('lpv_utm_medium')->nullable();
            $table->string('lpv_utm_campaign')->nullable();
            $table->string('lpv_utm_term')->nullable();
            $table->string('lpv_utm_content')->nullable();

            $table->unsignedInteger('lpv_pageviews')->default(1);
            $table->timestamp('lpv_started_at')->nullable();
            $table->timestamp('lpv_last_seen_at')->nullable();

            $table->timestamps();

            $table->foreign('lp_id')->references('lp_id')->on('landing_pages')->onDelete('cascade');
            $table->index(['lp_id', 'lpv_country_code']);
            $table->index(['lp_id', 'created_at']);
        });

        Schema::create('landing_page_events', function (Blueprint $table) {
            $table->bigIncrements('lpe_id');
            $table->unsignedBigInteger('lp_id');
            $table->unsignedBigInteger('lpv_id')->nullable();

            $table->string('lpe_visitor_id', 64)->index();
            $table->string('lpe_name', 64)->index();
            $table->longText('lpe_properties')->nullable();

            $table->timestamps();

            $table->foreign('lp_id')->references('lp_id')->on('landing_pages')->onDelete('cascade');
            $table->foreign('lpv_id')->references('lpv_id')->on('landing_page_visits')->onDelete('set null');
            $table->index(['lp_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_page_events');
        Schema::dropIfExists('landing_page_visits');
    }
}


