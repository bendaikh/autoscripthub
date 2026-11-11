<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteVisitsTable extends Migration
{
    public function up()
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('visited_at');
            $table->string('path', 191);
            $table->char('visitor_hash', 64);
            $table->char('country_code', 2)->nullable();
            $table->string('referrer', 191)->nullable();
            $table->string('device', 16)->nullable();

            $table->index('visited_at');
            $table->index('visitor_hash');
            $table->index(['visited_at', 'country_code']);
            $table->index(['visited_at', 'visitor_hash']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_visits');
    }
}
